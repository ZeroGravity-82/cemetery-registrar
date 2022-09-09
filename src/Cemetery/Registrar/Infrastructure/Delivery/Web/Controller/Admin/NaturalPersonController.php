<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Delivery\Web\Controller\Admin;

use Cemetery\Registrar\Application\ApplicationRequestBus;
use Cemetery\Registrar\Application\NaturalPerson\Query\ListAliveNaturalPersons\ListAliveNaturalPersonsRequest;
use Cemetery\Registrar\Infrastructure\Delivery\Web\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse as HttpJsonResponse;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class NaturalPersonController extends Controller
{
    public function __construct(
        private readonly ApplicationRequestBus $appRequestBus,
    ) {}

    #[Route('/natural-person/list-alive', name: 'natural_person_list_alive', methods: 'GET')]
    public function listAlive(HttpRequest $request): HttpJsonResponse
    {
        $queryRequest = new ListAliveNaturalPersonsRequest();
        $queryResponse = $this->appRequestBus->execute($queryRequest);

        return $this->buildJsonResponse($queryResponse, HttpResponse::HTTP_OK);
    }
}
