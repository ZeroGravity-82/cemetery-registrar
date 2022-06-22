<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Delivery\Web\Controller\Admin;

use Cemetery\Registrar\Application\Query\BurialPlace\GraveSite\CountCemeteryBlockTotal\CountCemeteryBlockTotalRequest;
use Cemetery\Registrar\Application\Query\BurialPlace\GraveSite\CountCemeteryBlockTotal\CountCemeteryBlockTotalService;
use Cemetery\Registrar\Application\Query\BurialPlace\GraveSite\ListCemeteryBlocks\ListCemeteryBlocksRequest;
use Cemetery\Registrar\Application\Query\BurialPlace\GraveSite\ListCemeteryBlocks\ListCemeteryBlocksService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class AdminBurialPlaceController extends AbstractController
{
    /**
     * @param CountCemeteryBlockTotalService $countCemeteryBlockTotalService
     * @param ListCemeteryBlocksService      $listCemeteryBlocksService
     */
    public function __construct(
        private readonly CountCemeteryBlockTotalService $countCemeteryBlockTotalService,
        private readonly ListCemeteryBlocksService      $listCemeteryBlocksService,
    ) {}

    #[Route('/admin/burial-place/grave-site', name: 'admin_burial_place_grave_site_list', methods: 'GET')]
    public function index(): Response
    {
        $cemeteryBlockTotalCount = $this->countCemeteryBlockTotalService
            ->execute(new CountCemeteryBlockTotalRequest())
            ->totalCount;
        $cemeteryBlockList = $this->listCemeteryBlocksService
            ->execute(new ListCemeteryBlocksRequest())
            ->list;

        return $this->render('admin/grave_site/list.html.twig', [
            'cemeteryBlockTotalCount' => $cemeteryBlockTotalCount,
            'cemeteryBlockList'       => $cemeteryBlockList,
        ]);
    }
}
