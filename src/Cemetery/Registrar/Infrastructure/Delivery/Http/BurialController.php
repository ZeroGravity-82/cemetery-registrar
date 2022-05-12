<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Delivery\Http;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BurialController extends AbstractController
{
    #[Route('/burial', name: 'burial_index', methods: 'GET')]
    public function index(): Response
    {
        return $this->render('burial/index.html.twig', [
        ]);
    }
}
