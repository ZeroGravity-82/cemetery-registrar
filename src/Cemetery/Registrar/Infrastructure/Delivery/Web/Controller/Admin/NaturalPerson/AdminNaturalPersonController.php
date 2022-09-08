<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Delivery\Web\Controller\Admin\NaturalPerson;

use Cemetery\Registrar\Application\ApplicationRequestBus;
use Cemetery\Registrar\Application\NaturalPerson\Query\PaginateNaturalPersons\PaginateNaturalPersonsRequest;
use Cemetery\Registrar\Infrastructure\Delivery\Web\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class AdminNaturalPersonController extends Controller
{
    public function __construct(
        private readonly ApplicationRequestBus $appRequestBus,
    ) {}

    #[Route('/admin/natural-person', name: 'admin_natural_person_paginated_list', methods: 'GET')]
    public function paginate(Request $request): Response
    {
        $page          = $request->query->get('page');
        $pageSize      = $request->query->get('pageSize');
        $term          = $request->query->get('term');
        $queryRequest  = new PaginateNaturalPersonsRequest($page, $pageSize, $term);
        $queryResponse = $this->appRequestBus->execute($queryRequest);

        $paginatedList = $queryResponse->data->paginatedList;
        $totalCount    = $queryResponse->data->totalCount;

        return $this->render('admin/natural_person/natural_person_paginated_list.html.twig', [
            'paginatedList' => $paginatedList,
            'totalCount'    => $totalCount,
        ]);
    }
}
