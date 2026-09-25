<?php

namespace App\Controller;

use App\Form\ClientRequestType;
use App\Repository\ArtistImageRepository;
use App\Repository\ArtistRepository;
use App\Repository\MixImageRepository;
use App\Service\MailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\RateLimiter\RateLimiterFactoryInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        Request $request,
        MixImageRepository $mixImageRepository,
        ArtistRepository $artistRepository, 
        TagAwareCacheInterface $artistCache, 
        TagAwareCacheInterface $mixCache,
        ArtistImageRepository $artistImageRepository,
        MailService $mailer,
        #[Target('contact_form_limiter')] RateLimiterFactoryInterface $contactFormLimiter
    ): Response {
        $form = $this->createForm(ClientRequestType::class);
        $form->handleRequest($request);

        $errors = [];
        $response = new Response();

        // Retrieve and cache the mixed image album (expires in 8600 seconds)
        $albumMix = $mixCache->get('mixAlbum', function (ItemInterface $item) use ($mixImageRepository) {
            $item->expiresAfter(8600);
            $mixImages = $mixImageRepository->findAll();
            $mixAlbum = [];
            foreach ($mixImages as $image) {
                $mixAlbum[] = $image->getPath();
            }
            return array_reverse($mixAlbum);
        });

        // Retrieve and cache the list of active artists sorted by position
        $artistsCached = $artistCache->get('artists', function (ItemInterface $item) use ($artistRepository) {
            $item->expiresAfter(8600);
            return $artistRepository->findBy(['isActive' => true], ['position' => 'ASC']);
        });

        // Fetch detailed artist profiles using individual cache keys by artist ID
        $artists = [];
        foreach ($artistsCached as $artist) {
            $artists[] = $artistCache->get('artist_' . $artist->getId(), function (ItemInterface $item) use ($artist, $artistImageRepository) {
                $item->expiresAfter(8600);
                $artistImages = $artistImageRepository->findImagesByArtistId($artist->getId());
                $images = [];
                foreach ($artistImages as $image) {
                    $images[] = [
                        'path' => $image->getPath()
                    ];
                }
                return [
                    'id'           => $artist->getId(),
                    'name'         => $artist->getName(),
                    'splitName'    => $artist->getNameSyllables(),
                    'profileImage' => $artist->getImage(),
                    'whatsapp'     => $artist->getWhatsapp(),
                    'insta'        => $artist->getInstagram(),
                    'facebook'     => $artist->getFacebook(),
                    'images'       => array_reverse($images)
                ];
            });
        }

        // Handle and process form submissions
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $limiter = $contactFormLimiter->create($request->getClientIp());
                $limit = $limiter->consume(1);

                // Rate limit exceeded: return HTTP 429 so Hotwire Turbo processes the response frame
                if (false === $limit->isAccepted()) {
                    $errors[] = 'Hai inviato troppi messaggi. Per favore riprova più tardi.';
                    $response->setStatusCode(Response::HTTP_TOO_MANY_REQUESTS);

                    return $this->render('home/index.html.twig', [
                        'form'     => $form,
                        'errors'   => $errors,
                        'mixAlbum' => $albumMix,
                        'artists'  => $artists,
                    ], $response);
                }

                // Send email
                $sent = $mailer->sendMail($form);
                if ($sent) {
                    $response->setStatusCode(Response::HTTP_OK);
                    return $this->render('home/index.html.twig', [
                        'form'     => $this->createForm(ClientRequestType::class),
                        'success'  => 'Messaggio inviato con successo!',
                        'mixAlbum' => $albumMix,
                        'artists'  => $artists,
                    ], $response);
                }
            } else {
                // Form is invalid: set HTTP 422 for Hotwire Turbo compatibility
                $response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }

        // Collect form validation errors if present
        $formErrors = $form->getErrors(deep: true, flatten: true);
        foreach ($formErrors as $error) {
            $errors[] = $error->getMessage();
        }

        // Render template for initial GET request or failed form validation
        return $this->render('home/index.html.twig', [
            'form'     => $form,
            'errors'   => $errors,
            'mixAlbum' => $albumMix,
            'artists'  => $artists,
        ], $response);
    }
}