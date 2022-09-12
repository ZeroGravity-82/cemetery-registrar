<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Delivery\Web\Controller\Admin\NaturalPerson;

use Cemetery\Registrar\Application\ApplicationRequestBus;
use Cemetery\Registrar\Application\NaturalPerson\Query\PaginateNaturalPersons\PaginateNaturalPersonsRequest;
use Cemetery\Registrar\Infrastructure\Delivery\Web\Controller\Controller;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class AdminNaturalPersonController extends Controller
{
    public function __construct(
        private readonly ApplicationRequestBus $appRequestBus,
    ) {}

    #[Route('/admin/natural-person', name: 'admin_natural_person_paginate', methods: 'GET')]
    public function paginate(HttpRequest $request): HttpResponse
    {
        $page          = $request->query->getInt('page', 1);
        $term          = $request->query->get('q');
        $queryRequest  = new PaginateNaturalPersonsRequest($page, $term);
        $queryResponse = $this->appRequestBus->execute($queryRequest);

        $paginatedList = $queryResponse->data->paginatedList;
        $totalCount    = $queryResponse->data->totalCount;

        return $this->render('admin/natural_person/natural_person_paginated_list.html.twig', [
            'paginatedList' => $paginatedList,
            'totalCount'    => $totalCount,
        ]);
    }
}
