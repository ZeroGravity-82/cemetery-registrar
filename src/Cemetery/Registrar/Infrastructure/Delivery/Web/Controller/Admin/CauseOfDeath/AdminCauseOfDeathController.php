<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Delivery\Web\Controller\Admin\CauseOfDeath;

use Cemetery\Registrar\Application\ApplicationRequestBus;
use Cemetery\Registrar\Application\CauseOfDeath\Command\CreateCauseOfDeath\CreateCauseOfDeathRequest;
use Cemetery\Registrar\Application\CauseOfDeath\Command\EditCauseOfDeath\EditCauseOfDeathRequest;
use Cemetery\Registrar\Application\CauseOfDeath\Command\RemoveCauseOfDeath\RemoveCauseOfDeathRequest;
use Cemetery\Registrar\Application\CauseOfDeath\Query\PaginateCausesOfDeath\PaginateCausesOfDeathRequest;
use Cemetery\Registrar\Application\CauseOfDeath\Query\ShowCauseOfDeath\ShowCauseOfDeathRequest;
use Cemetery\Registrar\Infrastructure\Delivery\Web\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse as HttpJsonResponse;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class AdminCauseOfDeathController extends Controller
{
    public function __construct(
        private readonly ApplicationRequestBus $appRequestBus,
    ) {}

    #[Route('/admin/cause-of-death', name: 'admin_cause_of_death_paginate', methods: HttpRequest::METHOD_GET)]
    public function paginate(HttpRequest $request): HttpResponse
    {
        $page          = $request->query->getInt('page', 1);
        $term          = $request->query->get('search');
        $queryRequest  = new PaginateCausesOfDeathRequest($page, $term);
        $queryResponse = $this->appRequestBus->execute($queryRequest);
        $paginatedList = $queryResponse->data->paginatedList;
        $totalCount    = $queryResponse->data->totalCount;

        return $this->render('admin/cause_of_death/cause_of_death_paginated_list.html.twig', [
            'paginatedList' => $paginatedList,
            'totalCount'    => $totalCount,
        ]);
    }

    #[Route('/admin/cause-of-death/{id}', name: 'admin_cause_of_death_show', methods: HttpRequest::METHOD_GET)]
    public function show(HttpRequest $httpRequest): HttpJsonResponse
    {
        $queryRequest  = $this->handleJsonRequest($httpRequest, ShowCauseOfDeathRequest::class);
        $queryResponse = $this->appRequestBus->execute($queryRequest);

        return $this->buildJsonResponse($queryResponse, HttpResponse::HTTP_OK);
    }

    #[Route('/admin/cause-of-death/create', name: 'admin_cause_of_death_create', methods: HttpRequest::METHOD_POST)]
    public function create(HttpRequest $httpRequest): HttpJsonResponse
    {
        $this->assertValidCsrfToken($httpRequest, 'cause_of_death');
        $commandRequest  = $this->handleJsonRequest($httpRequest, CreateCauseOfDeathRequest::class);
        $commandResponse = $this->appRequestBus->execute($commandRequest);

        return $this->buildJsonResponse($commandResponse, HttpResponse::HTTP_CREATED);
    }

    #[Route('/admin/cause-of-death/{id}/edit', name: 'admin_cause_of_death_edit', methods: HttpRequest::METHOD_PUT)]
    public function edit(HttpRequest $httpRequest, string $id): HttpJsonResponse
    {
        $this->assertValidCsrfToken($httpRequest, 'cause_of_death');
        $commandRequest  = $this->handleJsonRequest($httpRequest, EditCauseOfDeathRequest::class);
        $commandResponse = $this->appRequestBus->execute($commandRequest);

        return $this->buildJsonResponse($commandResponse, HttpResponse::HTTP_OK);
    }

    #[Route('/admin/cause-of-death/{id}', name: 'admin_cause_of_death_remove', methods: HttpRequest::METHOD_DELETE)]
    public function remove(HttpRequest $httpRequest): HttpJsonResponse
    {
        $this->assertValidCsrfToken($httpRequest, 'cause_of_death');
        $commandRequest  = $this->handleJsonRequest($httpRequest, RemoveCauseOfDeathRequest::class);
        $commandResponse = $this->appRequestBus->execute($commandRequest);

        return $this->buildJsonResponse($commandResponse, HttpResponse::HTTP_NO_CONTENT);
    }
}
