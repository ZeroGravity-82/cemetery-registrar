<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Delivery\Http;

use Cemetery\Registrar\Application\FuneralCompany\FuneralCompanyFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class FuneralCompanyController extends AbstractController
{
    /**
     * @param FuneralCompanyFetcher $funeralCompanyFetcher
     */
    public function __construct(
        private readonly FuneralCompanyFetcher $funeralCompanyFetcher,
    ) {}

    #[Route('/funeral-company', name: 'funeral_company_index', methods: 'GET')]
    public function index(): Response
    {
        $funeralCompanyViewList   = $this->funeralCompanyFetcher->findAll(1);
        $funeralCompanyTotalCount = $this->funeralCompanyFetcher->getTotalCount();

        return $this->render('funeral_company/index.html.twig', [
            'funeralCompanyViewList'   => $funeralCompanyViewList,
            'funeralCompanyTotalCount' => $funeralCompanyTotalCount,
        ]);
    }
}
