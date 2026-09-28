<?php

namespace App\Controller\Admin;

use App\Entity\MixImage;
use App\Service\ImageCompressor;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use Symfony\Component\Validator\Constraints as Assert; 
use Override;
use Symfony\Contracts\Cache\TagAwareCacheInterface;


class MixImageCrudController extends AbstractCrudController
{
    public TagAwareCacheInterface $mixCache;

    /**
     * Injects the TagAwareCacheInterface dependency for managing cache invalidation.
     */
    public function __construct(TagAwareCacheInterface $mixCache)
    {
        $this->mixCache = $mixCache;
    }

    public static function getEntityFqcn(): string
    {
        return MixImage::class;
    }


    /**
     * Configures the fields displayed in the EasyAdmin forms and list views.
     */
    public function configureFields(string $pageName): iterable
    {
        $albumName = "mixAlbum";
        $imagesFolder =  $this->getParameter('uploads_folder') . $albumName;
        return [
            // Field for uploading multiple images at once in creation forms
           ImageField::new("files","Aggiungi")
                ->setBasePath("/assets/uploads/" . $albumName)
                ->setUploadDir($imagesFolder)
                ->setUploadedFileNamePattern('[randomhash].webp')
                ->maxSize('5M',"L'immagine deve essere massimo 5MB")
                ->mimeTypes("image/png,image/jpeg,image/webp,image/jpg")
                ->setFormTypeOption('multiple',true)
                ->hideOnIndex()
                ->setFormTypeOptions([
                    'constraints' => [
                        new Assert\Count(
                            max:6,
                            maxMessage:'Non puoi caricare più di 6 immagini contemporaneamente.'
                        ),
                        new Assert\NotBlank(
                           message:"Devi inserire delle immagini"
                        )
                    ],
                ]),
            
            ImageField::new("path","Aggiungi immagini")
                ->setBasePath("/assets/uploads/" . $albumName)
                ->setUploadDir($imagesFolder)
                ->setUploadedFileNamePattern('[randomhash].webp')
                ->maxSize('5M',"L'immagine deve essere massimo 5MB")
                ->mimeTypes("image/png,image/jpeg,image/webp,image/jpg")
                ->hideOnForm()
                
            ];
    }


    /**
     * Handles batch image upload logic: attaches the first image to the main entity, 
     * instantiates new MixImage entities for additional files, and clears the mix album cache.
     */
    #[Override]
    public function persistEntity(EntityManagerInterface $entityManager, object $entityInstance): void
    {
        $files = $entityInstance->getFiles();
        if ($files) {
            $imageCompressor = new ImageCompressor();
            foreach ($files as $key => $file) {
                $filePath = $this->getParameter('uploads_folder') . "mixAlbum/" . $file;
                if ($key === 0) {
                    $entityInstance->setPath($file);
                }else{
                    $image = new MixImage();
                    $image->setPath($file);
                    $entityManager->persist($image);
                }
                $imageCompressor->compress($filePath);
        }

        // Invalidate the mix album cache entry
         $this->mixCache->delete("mixAlbum");
         parent::persistEntity($entityManager, $entityInstance);
        }
       
    }


    /**
     * Removes the entity record from database, deletes the image file from local storage, 
     * and clears the mix album cache pool.
     */
    #[Override]
     public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $albumName= 'mixAlbum/';
        $imageName = $entityInstance->getPath(); 

        parent::deleteEntity($entityManager, $entityInstance);

        // Delete image from server and clear cache entry
        if ($imageName) {
            $filePath = $this->getParameter("uploads_folder") . $albumName . $imageName;
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $this->mixCache->delete("mixAlbum");
        }
    }


    /**
     * Configures global CRUD page titles and default sorting order.
     */
    #[Override]
    public function configureCrud(Crud $crud): Crud
    {
        return $crud 
            ->setPageTitle("new","Aggiungi immagini all'album MIX")
            ->setPageTitle("index","Album MIX")
            ->setPageTitle("edit","Modifica album MIX")
            ->setDefaultSort(['id' => 'DESC']);
    }



    /**
     * Customizes action button labels and removes the edit action from index rows.
     */
   #[Override]
   public function configureActions(Actions $actions): Actions
   {
    return $actions
        ->update(Crud::PAGE_NEW,Action::SAVE_AND_RETURN, static fn(Action $action) => $action->setLabel("Salva"))
        ->update(Crud::PAGE_NEW,Action::SAVE_AND_ADD_ANOTHER, static fn(Action $action) => $action->setLabel("Salva e aggiungi altre immagini"))
        ->remove(Crud::PAGE_INDEX,Action::EDIT)
        ;
   }
}
