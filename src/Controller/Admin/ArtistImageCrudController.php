<?php

namespace App\Controller\Admin;

use App\Entity\ArtistImage;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use Override;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class ArtistImageCrudController extends AbstractCrudController
{
    public TagAwareCacheInterface $cache;

    /**
     * Injects the TagAwareCacheInterface dependency for managing cache invalidation.
     */
    public function __construct(TagAwareCacheInterface $artistCache)
    {
        $this->cache = $artistCache;
    }
    
    public static function getEntityFqcn(): string
    {
        return ArtistImage::class;
    }

    /**
     * Configures the fields displayed in the EasyAdmin forms and list views.
     */
    public function configureFields(string $pageName): iterable
    {
        $albumName = "artistImages";
        $imagesFolder = $this->getParameter('uploads_folder') . $albumName;
        return [
            IdField::new('id')->hideOnForm()->hideOnIndex(),
            AssociationField::new('Artist',"Artista"),

            // Field for multiple image upload on creation forms
            ImageField::new('files',"Immagine")
                ->setBasePath('/assets/uploads/' . $albumName)
                ->setUploadDir($imagesFolder)
                ->maxSize('5M',"L'immagine deve essere massimo 5MB")
                ->mimeTypes("image/png,image/jpeg,image/webp,image/jpg")
                ->setUploadedFileNamePattern('[randomhash].[extension]')
                ->setFormTypeOption('multiple',true)
                ->hideOnIndex()
                ->setFormTypeOptions([
                    'constraints' => [
                        new Assert\Count(
                            max:10,
                            maxMessage:'Non puoi caricare più di 10 immagini contemporaneamente.'
                        ),
                         new Assert\NotBlank(
                            message:"Devi inserire delle immagini"
                            )
                        ],
                ]),


            ImageField::new('path',"Immagine")
                ->setBasePath('/assets/uploads/' . $albumName)
                ->setUploadDir($imagesFolder)
                ->maxSize('5M',"L'immagine deve essere massimo 5MB")
                ->mimeTypes("image/png,image/jpeg,image/webp,image/jpg")
                ->setUploadedFileNamePattern('[randomhash].[extension]')
                ->hideOnForm(),
        ];
    }


    /**
     * Configures global settings for this CRUD controller (page titles, default sorting).
     */
    #[Override]
    public function configureCrud(Crud $crud): Crud
    {
        return $crud 
            ->setPageTitle("new","Aggiungi Immagini di un'artista")
            ->setPageTitle("index","Tattoos degli artisti")
            ->setPageTitle("edit","Modifica Tattoo di un'artista")
            ->setDefaultSort(['id' => 'DESC'])
            ;
    }

    /**
     * Handles batch image upload logic: links the first image to the current entity, 
     * creates new ArtistImage instances for additional uploaded files, and invalidates the artist cache.
     */
    #[Override]
    public function persistEntity(EntityManagerInterface $entityManager, object $entityInstance): void
    {
        $files = $entityInstance->getFiles();
        $artist = $entityInstance->getArtist();
        if ($files) {
            foreach ($files as $key => $file) {
                    if ($key === 0) {
                        $entityInstance->setPath($file);
                    }else{
                        $ArtistImage = new ArtistImage();
                        $ArtistImage->setArtist($artist);
                        $ArtistImage->setPath($file);
                        $entityManager->persist($ArtistImage);
                    }
            }
            // Invalidate the cache for the associated artist
            $this->cache->delete("artist_" . $artist->getId());
        }
        
        parent::persistEntity($entityManager, $entityInstance);

        // Ensure the cache is invalidated after persisting
        if ($artist) {
            $this->cache->delete('artist_' . $artist->getId());
        }
    }

    

     /**
     * Deletes the entity record, removes the physical image file from storage, and clears the artist cache.
     */
    #[Override]
     public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $albumName= 'artistImages/';
        $imageName = $entityInstance->getPath(); 
        $artist = $entityInstance->getArtist();

        parent::deleteEntity($entityManager, $entityInstance);
        // Remove the image from server and invalidate cache
        if ($imageName) {
            $filePath = $this->getParameter("uploads_folder") . $albumName . $imageName;
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $this->cache->delete("artist_" . $artist->getId());
        }
    }


    /**
     * Customizes action buttons, labels, and permissions for list, new, and edit pages.
     */
    #[Override]
    public function configureActions(Actions $actions): Actions
    {

        return $actions
            ->update(Crud::PAGE_INDEX, Action::NEW,
                static fn (Action $action) => $action->setLabel("Aggiungi nuove immagini di un artista")
            )
            ->update(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER,
                static fn (Action $action) => $action->setLabel("Salva e aggiungi un'altra immagine")
            )
            ->update(Crud::PAGE_NEW, Action::SAVE_AND_RETURN,
                static fn (Action $action) => $action->setLabel("Salva")
            )
            ->update(Crud::PAGE_EDIT, Action::SAVE_AND_RETURN,
                static fn (Action $action) => $action->setLabel("Salva")
            )
            ->update(Crud::PAGE_EDIT, Action::SAVE_AND_CONTINUE,
                static fn (Action $action) => $action->setLabel("Salva e continua")
            )
            ->remove(Crud::PAGE_INDEX,Action::EDIT)
            ->add(Crud::PAGE_EDIT,Action::DELETE);
    }

    
    
}
