<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Delivery\Web\Controller\Admin\FuneralCompany;

use Cemetery\Registrar\Application\ApplicationRequestBus;
use Cemetery\Registrar\Application\FuneralCompany\Query\ListFuneralCompanies\ListFuneralCompaniesRequest;
use Cemetery\Registrar\Infrastructure\Delivery\Web\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class AdminFuneralCompanyController extends AbstractController
{
    public function __construct(
        private ApplicationRequestBus $appRequestBus,
    ) {}

    #[Route('/admin/funeral-company', name: 'admin_funeral_company_list', methods: 'GET')]
    public function list(): HttpResponse
    {
        $queryRequest  = new ListFuneralCompaniesRequest();
        $queryResponse = $this->appRequestBus->execute($queryRequest);

        $list       = $queryResponse->data->list;
        $totalCount = $queryResponse->data->totalCount;

        return $this->render('admin/funeral_company/list_funeral_company.html.twig', [
            'list'       => $list,
            'totalCount' => $totalCount,
        ]);
    }
}
