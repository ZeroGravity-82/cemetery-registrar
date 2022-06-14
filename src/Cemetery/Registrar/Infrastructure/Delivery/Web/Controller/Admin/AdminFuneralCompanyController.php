<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Delivery\Web\Controller\Admin;

use Cemetery\Registrar\Application\Query\FuneralCompany\CountFuneralCompanyTotal\CountFuneralCompanyTotalRequest;
use Cemetery\Registrar\Application\Query\FuneralCompany\CountFuneralCompanyTotal\CountFuneralCompanyTotalService;
use Cemetery\Registrar\Application\Query\FuneralCompany\ListFuneralCompanies\ListFuneralCompaniesRequest;
use Cemetery\Registrar\Application\Query\FuneralCompany\ListFuneralCompanies\ListFuneralCompaniesService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class AdminFuneralCompanyController extends AbstractController
{
    /**
     * @param CountFuneralCompanyTotalService $countFuneralCompanyTotalService
     * @param ListFuneralCompaniesService     $listFuneralCompaniesService
     */
    public function __construct(
        private readonly CountFuneralCompanyTotalService $countFuneralCompanyTotalService,
        private readonly ListFuneralCompaniesService     $listFuneralCompaniesService,
    ) {}

    #[Route('/admin/funeral-company', name: 'admin_funeral_company_list', methods: 'GET')]
    public function index(): Response
    {
        $funeralCompanyTotalCount = $this->countFuneralCompanyTotalService
            ->execute(new CountFuneralCompanyTotalRequest())
            ->funeralCompanyTotalCount;

        $funeralCompanyList = $this->listFuneralCompaniesService
            ->execute(new ListFuneralCompaniesRequest())
            ->funeralCompanyList;

        return $this->render('admin/funeral_company/list.html.twig', [
            'funeralCompanyTotalCount' => $funeralCompanyTotalCount,
            'funeralCompanyList'       => $funeralCompanyList,
        ]);
    }
}
