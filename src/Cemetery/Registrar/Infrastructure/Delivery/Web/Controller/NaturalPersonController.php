<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Delivery\Web\Controller;

use Cemetery\Registrar\Application\ApplicationRequestBus;
use Cemetery\Registrar\Application\NaturalPerson\Command\ClarifyNaturalPersonBirthDetails\ClarifyNaturalPersonBirthDetailsRequest;
use Cemetery\Registrar\Application\NaturalPerson\Command\ClarifyNaturalPersonContact\ClarifyNaturalPersonContactRequest;
use Cemetery\Registrar\Application\NaturalPerson\Command\ClarifyNaturalPersonDeceasedDetails\ClarifyNaturalPersonDeceasedDetailsRequest;
use Cemetery\Registrar\Application\NaturalPerson\Command\ClarifyNaturalPersonFullName\ClarifyNaturalPersonFullNameRequest;
use Cemetery\Registrar\Application\NaturalPerson\Command\ClarifyNaturalPersonPassport\ClarifyNaturalPersonPassportRequest;
use Cemetery\Registrar\Application\NaturalPerson\Command\RemoveNaturalPerson\RemoveNaturalPersonRequest;
use Cemetery\Registrar\Application\NaturalPerson\Query\ListAliveNaturalPersons\ListAliveNaturalPersonsRequest;
use Cemetery\Registrar\Application\NaturalPerson\Query\ShowNaturalPerson\ShowNaturalPersonRequest;
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
        $term          = $request->query->get('search');
        $queryRequest  = new ListAliveNaturalPersonsRequest($term);
        $queryResponse = $this->appRequestBus->execute($queryRequest);

        return $this->buildJsonResponse($queryResponse, HttpResponse::HTTP_OK);
    }

    #[Route('/natural-person/{id}', name: 'natural_person_show', methods: HttpRequest::METHOD_GET)]
    public function show(HttpRequest $httpRequest): HttpJsonResponse
    {
        $queryRequest  = $this->handleJsonRequest($httpRequest, ShowNaturalPersonRequest::class);
        $queryResponse = $this->appRequestBus->execute($queryRequest);

        return $this->buildJsonResponse($queryResponse, HttpResponse::HTTP_OK);
    }

    #[Route(
        '/natural-person/{id}/clarify-full-name',
        name: 'natural_person_clarify_full_name',
        methods: HttpRequest::METHOD_PATCH
    )]
    public function clarifyFullName(HttpRequest $httpRequest, string $id): HttpJsonResponse
    {
        $this->assertValidCsrfToken($httpRequest, 'natural_person');
        $commandRequest  = $this->handleJsonRequest($httpRequest, ClarifyNaturalPersonFullNameRequest::class);
        $commandResponse = $this->appRequestBus->execute($commandRequest);

        return $this->buildJsonResponse($commandResponse, HttpResponse::HTTP_OK);
    }

    #[Route(
        '/natural-person/{id}/clarify-contact',
        name: 'natural_person_clarify_contact',
        methods: HttpRequest::METHOD_PATCH
    )]
    public function clarifyContact(HttpRequest $httpRequest, string $id): HttpJsonResponse
    {
        $this->assertValidCsrfToken($httpRequest, 'natural_person');        // TODO extract token validation to base class
        $commandRequest  = $this->handleJsonRequest($httpRequest, ClarifyNaturalPersonContactRequest::class);
        $commandResponse = $this->appRequestBus->execute($commandRequest);

        return $this->buildJsonResponse($commandResponse, HttpResponse::HTTP_OK);
    }

    #[Route(
        '/natural-person/{id}/clarify-birth-details',
        name: 'natural_person_clarify_birth_details',
        methods: HttpRequest::METHOD_PATCH
    )]
    public function clarifyBirthDetails(HttpRequest $httpRequest, string $id): HttpJsonResponse
    {
        $this->assertValidCsrfToken($httpRequest, 'natural_person');
        $commandRequest  = $this->handleJsonRequest($httpRequest, ClarifyNaturalPersonBirthDetailsRequest::class);
        $commandResponse = $this->appRequestBus->execute($commandRequest);

        return $this->buildJsonResponse($commandResponse, HttpResponse::HTTP_OK);
    }

    #[Route(
        '/natural-person/{id}/clarify-passport',
        name: 'natural_person_clarify_passport',
        methods: HttpRequest::METHOD_PATCH
    )]
    public function clarifyPassport(HttpRequest $httpRequest, string $id): HttpJsonResponse
    {
        $this->assertValidCsrfToken($httpRequest, 'natural_person');
        $commandRequest  = $this->handleJsonRequest($httpRequest, ClarifyNaturalPersonPassportRequest::class);
        $commandResponse = $this->appRequestBus->execute($commandRequest);

        return $this->buildJsonResponse($commandResponse, HttpResponse::HTTP_OK);
    }

    #[Route(
        '/natural-person/{id}/clarify-deceased-details',
        name: 'natural_person_clarify_deceased_details',
        methods: HttpRequest::METHOD_PATCH
    )]
    public function clarifyDeceasedDetails(HttpRequest $httpRequest, string $id): HttpJsonResponse
    {
        $this->assertValidCsrfToken($httpRequest, 'natural_person');
        $commandRequest  = $this->handleJsonRequest($httpRequest, ClarifyNaturalPersonDeceasedDetailsRequest::class);
        $commandResponse = $this->appRequestBus->execute($commandRequest);

        return $this->buildJsonResponse($commandResponse, HttpResponse::HTTP_OK);
    }









    #[Route(
        '/natural-person/{id}',
        name: 'natural_person_remove',
        methods: HttpRequest::METHOD_DELETE
    )]
    public function remove(HttpRequest $httpRequest): HttpJsonResponse
    {
        $this->assertValidCsrfToken($httpRequest, 'natural_person');
        $commandRequest  = $this->handleJsonRequest($httpRequest, RemoveNaturalPersonRequest::class);
        $commandResponse = $this->appRequestBus->execute($commandRequest);

        return $this->buildJsonResponse($commandResponse, HttpResponse::HTTP_NO_CONTENT);
    }
}
