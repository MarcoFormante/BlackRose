<?php

namespace App\Controller\Admin;

use App\Entity\Artist;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Override;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class ArtistCrudController extends AbstractCrudController
{

    public TagAwareCacheInterface $artistCache;  

    /**
     * Injects the TagAwareCacheInterface dependency for managing artist cache invalidation.
     */
    public function __construct(TagAwareCacheInterface $artistCache)
    {
       $this->artistCache = $artistCache;
    }


    public static function getEntityFqcn(): string
    {
        return Artist::class;
    }

    /**
     * Configures the fields displayed in the EasyAdmin forms and list views.
     */
    public function configureFields(string $pageName): iterable
    {
        $imagesFolder = $this->getParameter('uploads_folder') . "artists";
        return [
            TextField::new('name',"Nome"),
            TextField::new('nameSyllables',"Sillabe (separate con virgole)"),
            ImageField::new('image',"Foto Artista")
                ->setBasePath('/uploads/artists/')
                ->setUploadDir($imagesFolder)
                ->setUploadedFileNamePattern('[randomhash].[extension]')
                ->maxSize('5M',"L'immagine deve essere massimo 5MB")
                ->mimeTypes("image/png,image/jpeg,image/webp,image/jpg")
                ->setRequired($pageName === "edit" ? false : true)
                ,
            TextField::new('whatsapp'),
            TextField::new('instagram'),
            TextField::new('facebook'),
            IntegerField::new('position','Posizione'),
            BooleanField::new('isActive',"Attivo"),
        ];
    }



    /**
     * Configures global settings for this CRUD controller (page titles, default sorting).
     */
    #[Override]
    public function configureCrud(Crud $crud): Crud
    {
        return $crud 
            ->setPageTitle("new","Aggiungi  un'artista")
            ->setPageTitle("index", "Artisti")
            ->setPageTitle("edit", "Modifica Artista")
            ->setDefaultSort(['position' => 'ASC'])
            ;
    }


    /**
     * Clears the main artists cache pool when creating a new artist record.
     */
    #[Override]
    public function persistEntity(EntityManagerInterface $entityManager, object $entityInstance): void
    {
        $this->artistCache->delete("artists");
        parent::persistEntity($entityManager, $entityInstance);
    }


    /**
     * Invalidates the main artists cache entry when an existing artist is updated.
     */
    #[Override]
    public function updateEntity(EntityManagerInterface $entityManager, object $entityInstance): void
    {
        $this->artistCache->delete("artists");
        parent::updateEntity($entityManager, $entityInstance);
    }


    /**
     * Custom deletion handler to clean up the associated image file from storage when an entity is deleted.
     */
    #[Override]
    public function deleteEntity(EntityManagerInterface $entityManager, object $entityInstance): void
    {
        $albumName = 'artists/';
        $imageName = $entityInstance->getImage(); 
        $isActive = $entityInstance->isActive();

        parent::deleteEntity($entityManager, $entityInstance);
        // Delete the image from the server if it exists
        if ($imageName) {
            $filePath = $this->getParameter("uploads_folder") . $albumName . $imageName;
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
        if ($isActive) {
            $this->artistCache->delete("artists");
        }
      
    }


    /**
     * Customizes action buttons and button labels across index, new, and edit pages.
     */
    #[Override]
    public function configureActions(Actions $actions): Actions
    {
        return $actions
    
        ->update(Crud::PAGE_INDEX, Action::NEW,
            static fn (Action $action) => $action->setLabel("Aggiungi un nuovo artista")
        )
        ->update(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER,
            static fn (Action $action) => $action->setLabel("Salva e aggiungi un'altro Artista")
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

        ->add(Crud::PAGE_EDIT,Action::DELETE);
    }
}
