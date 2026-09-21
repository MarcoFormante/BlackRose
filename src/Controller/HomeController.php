<?php

namespace App\Controller;

use App\Form\ClientRequestType;
use App\Repository\ArtistImageRepository;
use App\Repository\ArtistRepository;
use App\Repository\MixImageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request,MixImageRepository $mixImageRepository,ArtistRepository $artistRepository, TagAwareCacheInterface  $artistCache, TagAwareCacheInterface $mixCache,ArtistImageRepository $artistImageRepository): Response
    {
        $form = $this->createForm(ClientRequestType::class);
        $form->handleRequest($request);
        $errors = [];

        // Collect form validation errors if present
        if ($errorData = $form->getErrors(deep:true,flatten:true)) {
            $errors = $errorData;
        }

        // Handle and process the client request form (!to finish!)
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            return $this->redirectToRoute("app_home"); 
        }

        // Retrieve and cache the mixed image album (8600 seconds)
        $albumMix = $mixCache->get('mixAlbum',function (ItemInterface $item) use($mixImageRepository){
            $item->expiresAfter(8600);
            $mixImages = $mixImageRepository->findAll();
            $mixAlbum = [];
            foreach ($mixImages as $image) {
                $mixAlbum[] = $image->getPath();
            }
            return array_reverse($mixAlbum);
        });


        // Retrieve and cache the list of artists sorted by position
        $artistsCached = $artistCache->get('artists', function (ItemInterface $item) use ($artistRepository) {
            $item->expiresAfter(8600);
            return $artistRepository->findBy(['isActive' => true], ['position' => 'ASC']);
        });


        // Get artist profiles using individual cache entries by artist id (8600 seconds)
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

        // Render the template with form, errors, and cached data
        return $this->render('home/index.html.twig', [
            'form' => $form,
            'errors' => $errors,
            'mixAlbum' => $albumMix,
            'artists' =>$artists
        ]);
    }
}
