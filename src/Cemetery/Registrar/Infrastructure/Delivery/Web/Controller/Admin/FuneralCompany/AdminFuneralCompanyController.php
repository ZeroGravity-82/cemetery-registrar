<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Delivery\Web\Controller\Admin\FuneralCompany;

use Cemetery\Registrar\Application\ApplicationRequestBus;
use Cemetery\Registrar\Application\FuneralCompany\Query\ListFuneralCompanies\ListFuneralCompaniesRequest;
use Cemetery\Registrar\Infrastructure\Delivery\Web\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class AdminFuneralCompanyController extends Controller
{
    public function __construct(
        private readonly ApplicationRequestBus $appRequestBus,
    ) {}

    #[Route('/admin/funeral-company', name: 'admin_funeral_company_list', methods: 'GET')]
    public function list(): Response
    {
        $queryRequest  = new ListFuneralCompaniesRequest();
        $queryResponse = $this->appRequestBus->execute($queryRequest);

        $list       = $queryResponse->data->list;
        $totalCount = $queryResponse->data->totalCount;

        return $this->render('admin/funeral_company/list.html.twig', [
            'list'       => $list,
            'totalCount' => $totalCount,
        ]);
    }
}
