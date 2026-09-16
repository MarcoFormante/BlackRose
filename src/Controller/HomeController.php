<?php

namespace App\Controller;

use App\Form\ClientRequestType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(ClientRequestType::class);
        $form->handleRequest($request);
        $errors = [];

        if ($errorData = $form->getErrors(deep:true,flatten:true)) {
            $errors = $errorData;
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            return $this->redirectToRoute("app_home"); 
        }
        return $this->render('home/index.html.twig', [
           'form' => $form,
           'errors' => $errors
        ]);
    }
}
