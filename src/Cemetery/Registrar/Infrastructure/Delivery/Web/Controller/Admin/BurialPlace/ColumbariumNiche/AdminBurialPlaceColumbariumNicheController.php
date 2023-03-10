<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Delivery\Web\Controller\Admin\BurialPlace\ColumbariumNiche;

use Cemetery\Registrar\Application\ApplicationRequestBus;
use Cemetery\Registrar\Application\BurialPlace\ColumbariumNiche\Query\ListColumbariumNiches\ListColumbariumNichesRequest;
use Cemetery\Registrar\Infrastructure\Delivery\Web\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class AdminBurialPlaceColumbariumNicheController extends AbstractController
{
    public function __construct(
        private ApplicationRequestBus $appRequestBus,
    ) {}

    #[Route('/admin/burial-place/columbarium-niche', name: 'admin_burial_place_columbarium_niche_list', methods: 'GET')]
    public function columbariumNicheList(): HttpResponse
    {
        $queryRequest          = new ListColumbariumNichesRequest();
        $queryResponse         = $this->appRequestBus->execute($queryRequest);
        $list                  = $queryResponse->data->list;
        $totalCount            = $queryResponse->data->totalCount;
        $columbariumList       = $queryResponse->data->columbariumList;
        $columbariumTotalCount = $queryResponse->data->columbariumTotalCount;

        return $this->render('admin/burial_place/columbarium_niche/list_columbarium_niche.html.twig', [
            'list'                  => $list,
            'totalCount'            => $totalCount,
            'columbariumList'       => $columbariumList,
            'columbariumTotalCount' => $columbariumTotalCount,
        ]);
    }
}
