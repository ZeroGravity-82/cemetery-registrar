<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Delivery\Http;

use Cemetery\Registrar\Application\Burial\BurialFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BurialController extends AbstractController
{
    /**
     * @param BurialFetcher $burialFetcher
     */
    public function __construct(
        private readonly BurialFetcher $burialFetcher,
    ) {}

    #[Route('/burial', name: 'burial_index', methods: 'GET')]
    public function index(): Response
    {
        $burialViewList = $this->burialFetcher->findAll(1);

        return $this->render('burial/index.html.twig', [
            'burialViewList' => $burialViewList,
        ]);
    }

    #[Route('/burial/new', name: 'burial_new', methods: 'POST')]
    public function new(Request $request): Response
    {



        return $this->redirectToRoute('burial_index');
    }
}
