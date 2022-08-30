<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Delivery\Web\Controller\Admin\BurialPlace\MemorialTree;

use Cemetery\Registrar\Application\ApplicationRequestBus;
use Cemetery\Registrar\Application\BurialPlace\MemorialTree\Query\ListMemorialTrees\ListMemorialTreesRequest;
use Cemetery\Registrar\Infrastructure\Delivery\Web\Controller\Controller;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class AdminBurialPlaceMemorialTreeController extends Controller
{
    public function __construct(
        private readonly ApplicationRequestBus $appRequestBus,
    ) {}

    #[Route('/admin/burial-place/memorial-tree', name: 'admin_burial_place_memorial_tree_list', methods: 'GET')]
    public function memorialTreeList(): HttpResponse
    {
        $queryRequest  = new ListMemorialTreesRequest();
        $queryResponse = $this->appRequestBus->execute($queryRequest);
        $list          = $queryResponse->data->list;
        $totalCount    = $queryResponse->data->totalCount;

        return $this->render('admin/burial_place/memorial_tree/list_memorial_tree.html.twig', [
            'list'       => $list,
            'totalCount' => $totalCount,
        ]);
    }
}
