<?php

namespace App\Controller\Admin;

use App\Entity\Game;
use App\Entity\User;
use App\Entity\Event;
use App\Entity\Contact;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

#[IsGranted('ROLE_ADMIN', message: "Vous n'avez pas l'autorisation d'accéder à cette page.")]
class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin.index')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Games and Friends - Admin')
            ->renderContentMaximized();
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Utilisateurs', 'fa-solid fa-users', User::class);
        yield MenuItem::linkToCrud('Evenements', 'fa-regular fa-calendar-days', Event::class);
        yield MenuItem::linkToCrud('Jeux', 'fa-solid fa-dice', Game::class);
        yield MenuItem::linkToCrud('Contact', 'fa-solid fa-envelopes-bulk', Contact::class);
    }
}
