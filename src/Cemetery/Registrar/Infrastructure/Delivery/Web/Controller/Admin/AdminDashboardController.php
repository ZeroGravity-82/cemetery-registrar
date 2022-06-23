<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Delivery\Web\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
#[Route('/admin/dashboard')]
class AdminDashboardController extends AbstractController
{
    #[Route('/', name: 'admin_dashboard', methods: 'GET')]
    public function dashboard(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }
}
