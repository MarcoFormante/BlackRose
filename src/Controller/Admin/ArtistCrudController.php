<?php

namespace App\Controller\Admin;

use App\Entity\Artist;
use App\Service\ImageCompressor;
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
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class ArtistCrudController extends AbstractCrudController
{

    public TagAwareCacheInterface $artistCache;  
    private string $albumPath;
    /**
     * Injects the TagAwareCacheInterface dependency for managing artist cache invalidation.
     */
    public function __construct(TagAwareCacheInterface $artistCache,#[Autowire(param: 'uploads_folder')] string $uploadsFolder)
    {
        $this->artistCache = $artistCache;
        $this->albumPath = $uploadsFolder . "artists/";
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
        $albumName = "artists";
        return [
            TextField::new('name',"Nome"),
            TextField::new('nameSyllables',"Sillabe (separate con virgole)"),
            ImageField::new('image',"Foto Artista")
                ->setBasePath('/assets/uploads/' . $albumName)
                ->setUploadDir( $this->albumPath)
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
        $imageName = $entityInstance->getImage();
        $imagePath =  $this->albumPath . $imageName;
        $imageCompressor = new ImageCompressor();
        $imageCompressor->compress($imagePath);
        $this->artistCache->delete("artists");
        parent::persistEntity($entityManager, $entityInstance);
    }


    /**
     * Invalidates the main artists cache entry when an existing artist is updated.
     */
    #[Override]
    public function updateEntity(EntityManagerInterface $entityManager, object $entityInstance): void
    {
        $unitofWork = $entityManager->getUnitOfWork();
        $unitofWork->computeChangeSets();
        $changeSet = $unitofWork->getEntityChangeSet($entityInstance);
        if (isset($changeSet['image'][1]) && $changeSet['image'][1] ) {
            $imageName = $changeSet['image'][1];
            $imageCompressor = new ImageCompressor();
            $imagePath = $this->albumPath . $imageName;
            $imageCompressor->compress($imagePath);
        }else{
            $entityInstance->setImage($changeSet['image'][0]);
        }
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
