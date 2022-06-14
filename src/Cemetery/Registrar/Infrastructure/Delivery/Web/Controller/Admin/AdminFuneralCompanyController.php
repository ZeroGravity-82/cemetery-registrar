<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Delivery\Web\Controller\Admin;

use Cemetery\Registrar\Domain\View\FuneralCompany\FuneralCompanyFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class AdminFuneralCompanyController extends AbstractController
{
    /**
     * @param FuneralCompanyFetcher $funeralCompanyFetcher
     */
    public function __construct(
        private readonly FuneralCompanyFetcher $funeralCompanyFetcher,
    ) {}

    #[Route('/admin/funeral-company', name: 'admin_funeral_company_list', methods: 'GET')]
    public function index(): Response
    {
        $funeralCompanyViewList   = $this->funeralCompanyFetcher->findAll(1);
        $funeralCompanyTotalCount = $this->funeralCompanyFetcher->getTotalCount();

        return $this->render('admin/funeral_company/list.html.twig', [
            'funeralCompanyViewList'   => $funeralCompanyViewList,
            'funeralCompanyTotalCount' => $funeralCompanyTotalCount,
        ]);
    }
}
