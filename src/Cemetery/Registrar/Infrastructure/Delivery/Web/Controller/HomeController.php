<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Delivery\Web\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends Controller
{
    #[Route('/', name: 'home', methods: 'GET')]
    public function index(): Response
    {
        return $this->render('home.html.twig', [
        ]);
    }
}
