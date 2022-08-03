<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Delivery\Web\Controller\Admin;

use Cemetery\Registrar\Application\ApplicationRequestBus;
use Cemetery\Registrar\Application\BurialPlace\ColumbariumNiche\Query\ListColumbariumNiches\ListColumbariumNichesRequest;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\Query\ListGraveSites\ListGraveSitesRequest;
use Cemetery\Registrar\Application\BurialPlace\MemorialTree\Query\ListMemorialTrees\ListMemorialTreesRequest;
use Cemetery\Registrar\Infrastructure\Delivery\Web\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class AdminBurialPlaceController extends Controller
{
    public function __construct(
        private readonly ApplicationRequestBus $appRequestBus,
    ) {}

    #[Route('/admin/burial-place/grave-site', name: 'admin_burial_place_grave_site_list', methods: 'GET')]
    public function graveSiteList(): Response
    {
        $queryRequest            = new ListGraveSitesRequest();
        $queryResponse           = $this->appRequestBus->execute($queryRequest);
        $list                    = $queryResponse->data->list;
        $totalCount              = $queryResponse->data->totalCount;
        $cemeteryBlockList       = $queryResponse->data->cemeteryBlockList;
        $cemeteryBlockTotalCount = $queryResponse->data->cemeteryBlockTotalCount;

        return $this->render('admin/burial_place/grave_site/list.html.twig', [
            'list'                    => $list,
            'totalCount'              => $totalCount,
            'cemeteryBlockList'       => $cemeteryBlockList,
            'cemeteryBlockTotalCount' => $cemeteryBlockTotalCount,
        ]);
    }

    #[Route('/admin/burial-place/columbarium-niche', name: 'admin_burial_place_columbarium_niche_list', methods: 'GET')]
    public function columbariumNicheList(): Response
    {
        $queryRequest          = new ListColumbariumNichesRequest();
        $queryResponse         = $this->appRequestBus->execute($queryRequest);
        $list                  = $queryResponse->data->list;
        $totalCount            = $queryResponse->data->totalCount;
        $columbariumList       = $queryResponse->data->columbariumList;
        $columbariumTotalCount = $queryResponse->data->columbariumTotalCount;

        return $this->render('admin/burial_place/columbarium_niche/list.html.twig', [
            'list'                  => $list,
            'totalCount'            => $totalCount,
            'columbariumList'       => $columbariumList,
            'columbariumTotalCount' => $columbariumTotalCount,
        ]);
    }

    #[Route('/admin/burial-place/memorial-tree', name: 'admin_burial_place_memorial_tree_list', methods: 'GET')]
    public function memorialTreeList(): Response
    {
        $queryRequest  = new ListMemorialTreesRequest();
        $queryResponse = $this->appRequestBus->execute($queryRequest);
        $list          = $queryResponse->data->list;
        $totalCount    = $queryResponse->data->totalCount;

        return $this->render('admin/burial_place/memorial_tree/list.html.twig', [
            'list'       => $list,
            'totalCount' => $totalCount,
        ]);
    }
}
