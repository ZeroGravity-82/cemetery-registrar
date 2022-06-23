<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Delivery\Web\Controller\Admin;

use Cemetery\Registrar\Application\Query\BurialPlace\GraveSite\CountCemeteryBlockTotal\CountCemeteryBlockTotalRequest;
use Cemetery\Registrar\Application\Query\BurialPlace\GraveSite\CountCemeteryBlockTotal\CountCemeteryBlockTotalService;
use Cemetery\Registrar\Application\Query\BurialPlace\GraveSite\CountGraveSiteTotal\CountGraveSiteTotalRequest;
use Cemetery\Registrar\Application\Query\BurialPlace\GraveSite\CountGraveSiteTotal\CountGraveSiteTotalService;
use Cemetery\Registrar\Application\Query\BurialPlace\GraveSite\ListCemeteryBlocks\ListCemeteryBlocksRequest;
use Cemetery\Registrar\Application\Query\BurialPlace\GraveSite\ListCemeteryBlocks\ListCemeteryBlocksService;
use Cemetery\Registrar\Application\Query\BurialPlace\GraveSite\ListGraveSites\ListGraveSitesRequest;
use Cemetery\Registrar\Application\Query\BurialPlace\GraveSite\ListGraveSites\ListGraveSitesService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
#[Route('/admin/burial-place')]
class AdminBurialPlaceController extends AbstractController
{
    /**
     * @param CountCemeteryBlockTotalService $countCemeteryBlockTotalService
     * @param ListCemeteryBlocksService      $listCemeteryBlocksService
     * @param CountGraveSiteTotalService     $countGraveSiteTotalService
     * @param ListGraveSitesService          $listGraveSitesService
     */
    public function __construct(
        private readonly CountCemeteryBlockTotalService $countCemeteryBlockTotalService,
        private readonly ListCemeteryBlocksService      $listCemeteryBlocksService,
        private readonly CountGraveSiteTotalService     $countGraveSiteTotalService,
        private readonly ListGraveSitesService          $listGraveSitesService,
    ) {}

    #[Route('/grave-site', name: 'admin_burial_place_grave_site_list', methods: 'GET')]
    public function graveSiteList(): Response
    {
        $cemeteryBlockTotalCount = $this->countCemeteryBlockTotalService
            ->execute(new CountCemeteryBlockTotalRequest())
            ->totalCount;
        $cemeteryBlockList = $this->listCemeteryBlocksService
            ->execute(new ListCemeteryBlocksRequest())
            ->list;
        $graveSiteTotalCount = $this->countGraveSiteTotalService
            ->execute(new CountGraveSiteTotalRequest())
            ->totalCount;
        $graveSiteList = $this->listGraveSitesService
            ->execute(new ListGraveSitesRequest())
            ->list;

        return $this->render('admin/burial_place/grave_site/list.html.twig', [
            'cemeteryBlockTotalCount' => $cemeteryBlockTotalCount,
            'cemeteryBlockList'       => $cemeteryBlockList,
            'graveSiteTotalCount'     => $graveSiteTotalCount,
            'graveSiteList'           => $graveSiteList,
        ]);
    }

    #[Route('/columbarium-niche', name: 'admin_burial_place_columbarium_niche_list', methods: 'GET')]
    public function columbariumNicheList(): Response
    {
        $columbariumTotalCount = $this->countColumbariumTotalService
            ->execute(new CountColumbariumTotalRequest())
            ->totalCount;
        $columbariumList = $this->listColumbariumsService
            ->execute(new ListColumbariumsRequest())
            ->list;
        $columbariumNicheTotalCount = $this->countColumbariumNicheTotalService
            ->execute(new CountColumbariumNicheTotalRequest())
            ->totalCount;
        $columbariumNicheList = $this->listColumbariumNichesService
            ->execute(new ListColumbariumNichesRequest())
            ->list;

        return $this->render('admin/burial_place/columbarium_niche/list.html.twig', [
            'columbariumTotalCount'      => $columbariumTotalCount,
            'columbariumList'            => $columbariumList,
            'columbariumNicheTotalCount' => $columbariumNicheTotalCount,
            'columbariumNicheList'       => $columbariumNicheList,
        ]);
    }
}
