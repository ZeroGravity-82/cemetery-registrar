<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Delivery\Web\Controller\Admin;

use Cemetery\Registrar\Application\ApplicationRequestBus;
use Cemetery\Registrar\Application\BurialPlace\ColumbariumNiche\Query\CountColumbariumNicheTotal\CountColumbariumNicheTotalRequest;
use Cemetery\Registrar\Application\BurialPlace\ColumbariumNiche\Query\CountColumbariumTotal\CountColumbariumTotalRequest;
use Cemetery\Registrar\Application\BurialPlace\ColumbariumNiche\Query\ListColumbarium\ListColumbariumRequest;
use Cemetery\Registrar\Application\BurialPlace\ColumbariumNiche\Query\ListColumbariumNiches\ListColumbariumNichesRequest;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\Query\CountCemeteryBlockTotal\CountCemeteryBlockTotalRequest;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\Query\CountGraveSiteTotal\CountGraveSiteTotalRequest;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\Query\ListCemeteryBlocks\ListCemeteryBlocksRequest;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\Query\ListGraveSites\ListGraveSitesRequest;
use Cemetery\Registrar\Application\BurialPlace\MemorialTree\Query\CountMemorialTreeTotal\CountMemorialTreeTotalRequest;
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
        $cemeteryBlockTotalCount = $this->appRequestBus->execute(new CountCemeteryBlockTotalRequest())->totalCount;
        $cemeteryBlockList       = $this->appRequestBus->execute(new ListCemeteryBlocksRequest())->list;
        $graveSiteTotalCount     = $this->appRequestBus->execute(new CountGraveSiteTotalRequest())->totalCount;
        $graveSiteList           = $this->appRequestBus->execute(new ListGraveSitesRequest())->list;

        return $this->render('admin/burial_place/grave_site/list.html.twig', [
            'cemeteryBlockTotalCount' => $cemeteryBlockTotalCount,
            'cemeteryBlockList'       => $cemeteryBlockList,
            'graveSiteTotalCount'     => $graveSiteTotalCount,
            'graveSiteList'           => $graveSiteList,
        ]);
    }

    #[Route('/admin/burial-place/columbarium-niche', name: 'admin_burial_place_columbarium_niche_list', methods: 'GET')]
    public function columbariumNicheList(): Response
    {
        $columbariumTotalCount      = $this->appRequestBus->execute(new CountColumbariumTotalRequest())->totalCount;
        $columbariumList            = $this->appRequestBus->execute(new ListColumbariumRequest())->list;
        $columbariumNicheTotalCount = $this->appRequestBus->execute(new CountColumbariumNicheTotalRequest())->totalCount;
        $columbariumNicheList       = $this->appRequestBus->execute(new ListColumbariumNichesRequest())->list;

        return $this->render('admin/burial_place/columbarium_niche/list.html.twig', [
            'columbariumTotalCount'      => $columbariumTotalCount,
            'columbariumList'            => $columbariumList,
            'columbariumNicheTotalCount' => $columbariumNicheTotalCount,
            'columbariumNicheList'       => $columbariumNicheList,
        ]);
    }

    #[Route('/admin/burial-place/memorial-tree', name: 'admin_burial_place_memorial_tree_list', methods: 'GET')]
    public function memorialTreeList(): Response
    {
        $memorialTreeTotalCount = $this->appRequestBus->execute(new CountMemorialTreeTotalRequest())->totalCount;
        $memorialTreeList       = $this->appRequestBus->execute(new ListMemorialTreesRequest())->list;

        return $this->render('admin/burial_place/memorial_tree/list.html.twig', [
            'memorialTreeTotalCount' => $memorialTreeTotalCount,
            'memorialTreeList'       => $memorialTreeList,
        ]);
    }
}
