<?php

namespace App\Controller\Admin;

use App\Entity\OpeningTime;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Override;

class OpeningTimeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return OpeningTime::class;
    }

    /**
     * Configures the form fields for opening and closing schedules.
     */
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('lunedi','Lunedi: Apertura e Chiusura'),
            TextField::new('sabato','Sabato: Apertura e Chiusura'),
            TextField::new('domenica','Domenica: Apertura e Chiusura'),
        ];
    }


    /**
     * Customizes action button labels and removes creation action from the list view.
     */
    #[Override]
    public function configureActions(Actions $actions): Actions
    {
        return $actions
        ->update(Crud::PAGE_NEW,Action::SAVE_AND_RETURN,static fn(Action $action) => $action->setLabel("Salva"))
        ->update(Crud::PAGE_NEW,Action::SAVE_AND_ADD_ANOTHER,static fn(Action $action) => $action->setLabel("Salva e aggiungi"))
        // Disable creation of new records to maintain a single opening schedule configuration
        ->remove(Crud::PAGE_INDEX,Action::NEW);
    }
}

