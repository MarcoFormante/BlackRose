<?php 

namespace App\Service;

use Composer\Pcre\Regex;
use DateTime;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Transport\TransportInterface;


class MailService
{
    public function __construct(
       private readonly TransportInterface $transport,
    ) {}

    public function sendMail(FormInterface $form): bool
    {
        $formData = $form->getData();
        $rawMessage = $form->get('message')->getData();
        $cleanMessage = nl2br(htmlspecialchars($rawMessage, ENT_QUOTES, 'UTF-8'));

        if (preg_match('/https?:\/\/[^\s]+/i', $cleanMessage)) {
            $form->get('message')->addError(new FormError('Non è consentito inserire link o indirizzi web'));
            return false;
        }
        $date = $formData['day'] ? htmlspecialchars($formData['day']->format("d-m-Y"), ENT_QUOTES, 'UTF-8') :  null;
        $time = $formData['time'] ? htmlspecialchars($formData['time']->format("H-i"), ENT_QUOTES, 'UTF-8') :  null;;

      
        $timeText = $time ? "<strong> alle </strong> $time" : null;
        $dateText = $date ?  "<p><strong>Possibile appuntamento il </strong> $date $timeText </p>" : null;

        $name = htmlspecialchars($form->get('name')->getData(), ENT_QUOTES, 'UTF-8');

        if (preg_match('/https?:\/\/[^\s]+/i', $name)) {
            $form->get('message')->addError(new FormError('Non è consentito inserire link o indirizzi web'));
            return false;
        }

        $userEmail = htmlspecialchars($form->get('email')->getData(), ENT_QUOTES, 'UTF-8');
        $file = $formData['file'];
      
        $bodyHtml = "
            <h2>Nuovo messaggio dal sito web BlackRoseTattoo</h2>
            <p><strong>Inviato da: </strong> {$name}</p>
            <p><strong>Email:</strong> {$userEmail}</p>
            <hr>
            <p><strong>Messaggio:</strong></p>

            <p>{$cleanMessage}</p>

            <div>
                $dateText
            </div>
        ";

        try {
            $email = (new Email())
                ->from("Black Rose Tattoo Messina <test@resend.dev>")
                ->to('formante.marco@gmail.com')
                ->subject("Nuova richiesta dal sito BlackRoseTatto da parte di $name (" . date("d-m-Y-H-i-s") .")")
                ->replyTo($userEmail)
                ->text("Nuovo messaggio da parte di $name")
                ->html($bodyHtml);

                if ($file) {
                    $email->attachFromPath(
                        $file->getPathname(),          
                        $file->getClientOriginalName(), 
                        $file->getClientMimeType() 
                    );
                }

            $this->transport->send($email);
            
            return true;
        } catch (\Throwable $th) {
            $form->addError(new FormError(
                'Impossibile contattare il server di posta. Verifica la connessione di rete.'
            ));
            return false;
        }
    }
}