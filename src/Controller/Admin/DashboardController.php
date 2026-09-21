<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    /**
     * Renders the custom admin dashboard homepage view.  (template/admin/my-dashboard-html.twig)
     */
    public function index(): Response
    {
        return $this->render('admin/my-dashboard.html.twig');
    }

    /**
     * Configures page Title for the admin dashboard layout
     */
    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('BlackRoseTattoo Admin');
    }


    /**
     * Configures the primary sidebar navigation menu items and links.
     */
    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkTo(ArtistCrudController::class, 'Artisti', 'fa fa-user');
        yield MenuItem::linkTo(ArtistImageCrudController::class, "Tattoos degli artisti", 'fa fa-file-image-o');
        yield MenuItem::linkTo(MixImageCrudController::class, "Album MIX", 'fa fa-file-image-o');
        yield MenuItem::linkTo(OpeningTimeCrudController::class, "Orari D'apertura", 'fa-solid fa-clock');
    }
}
