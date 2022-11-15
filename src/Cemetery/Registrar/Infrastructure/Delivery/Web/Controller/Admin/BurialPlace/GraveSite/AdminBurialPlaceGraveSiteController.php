<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Delivery\Web\Controller\Admin\BurialPlace\GraveSite;

use Cemetery\Registrar\Application\ApplicationRequestBus;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\ClarifyGraveSiteGeoPosition\ClarifyGraveSiteGeoPositionRequest;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\ClarifyGraveSiteLocation\ClarifyGraveSiteLocationRequest;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\ClarifyGraveSiteSize\ClarifyGraveSiteSizeRequest;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\ClearGraveSiteGeoPosition\ClearGraveSiteGeoPositionRequest;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\ClearGraveSiteSize\ClearGraveSiteSizeRequest;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\CreateGraveSite\CreateGraveSiteRequest;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\DiscardGraveSitePersonInCharge\DiscardGraveSitePersonInChargeRequest;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\RemoveGraveSite\RemoveGraveSiteRequest;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\Query\ListGraveSites\ListGraveSitesRequest;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\Query\ShowGraveSite\ShowGraveSiteRequest;
use Cemetery\Registrar\Infrastructure\Delivery\Web\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse as HttpJsonResponse;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class AdminBurialPlaceGraveSiteController extends AbstractController
{
    public function __construct(
        private ApplicationRequestBus $appRequestBus,
    ) {}

    #[Route(
        '/admin/burial-place/grave-site',
        name: 'admin_burial_place_grave_site_list',
        methods: 'GET'
    )]
    public function graveSiteList(): HttpResponse
    {
        $queryRequest            = new ListGraveSitesRequest();
        $queryResponse           = $this->appRequestBus->execute($queryRequest);
        $list                    = $queryResponse->data->list;
        $totalCount              = $queryResponse->data->totalCount;
        $cemeteryBlockList       = $queryResponse->data->cemeteryBlockList;
        $cemeteryBlockTotalCount = $queryResponse->data->cemeteryBlockTotalCount;

        return $this->render('admin/burial_place/grave_site/grave_site_paginated_list.html.twig', [
            'list'                    => $list,
            'totalCount'              => $totalCount,
            'cemeteryBlockList'       => $cemeteryBlockList,
            'cemeteryBlockTotalCount' => $cemeteryBlockTotalCount,
        ]);
    }

    #[Route(
        '/admin/burial-place/grave-site/{id}',
        name: 'admin_grave_site_show',
        methods: HttpRequest::METHOD_GET
    )]
    public function showGraveSite(HttpRequest $httpRequest): HttpJsonResponse
    {
        $queryRequest  = $this->handleJsonRequest($httpRequest, ShowGraveSiteRequest::class);
        $queryResponse = $this->appRequestBus->execute($queryRequest);

        return $this->buildJsonResponse($queryResponse, HttpResponse::HTTP_OK, $this->getCsrfToken('grave_site'));
    }

    #[Route(
        '/admin/burial-place/grave-site/create',
        name: 'admin_grave_site_create',
        methods: HttpRequest::METHOD_POST
    )]
    public function createGraveSite(HttpRequest $httpRequest): HttpJsonResponse
    {
        $this->assertValidCsrfToken($httpRequest, 'grave_site');
        $commandRequest  = $this->handleJsonRequest($httpRequest, CreateGraveSiteRequest::class);
        $commandResponse = $this->appRequestBus->execute($commandRequest);

        return $this->buildJsonResponse($commandResponse, HttpResponse::HTTP_CREATED);
    }

    #[Route(
        '/admin/burial-place/grave-site/{id}/clarify-location',
        name: 'admin_grave_site_clarify_location',
        methods: HttpRequest::METHOD_PATCH
    )]
    public function clarifyGraveSiteLocation(HttpRequest $httpRequest, string $id): HttpJsonResponse
    {
        $this->assertValidCsrfToken($httpRequest, 'grave_site');
        $commandRequest  = $this->handleJsonRequest($httpRequest, ClarifyGraveSiteLocationRequest::class);
        $commandResponse = $this->appRequestBus->execute($commandRequest);

        return $this->buildJsonResponse($commandResponse, HttpResponse::HTTP_OK);
    }

    #[Route(
        '/admin/burial-place/grave-site/{id}/clarify-size',
        name: 'admin_grave_site_clarify_size',
        methods: HttpRequest::METHOD_PATCH
    )]
    public function clarifyGraveSiteSize(HttpRequest $httpRequest, string $id): HttpJsonResponse
    {
        $this->assertValidCsrfToken($httpRequest, 'grave_site');
        $commandRequest  = $this->handleJsonRequest($httpRequest, ClarifyGraveSiteSizeRequest::class);
        $commandResponse = $this->appRequestBus->execute($commandRequest);

        return $this->buildJsonResponse($commandResponse, HttpResponse::HTTP_OK);
    }

    #[Route(
        '/admin/burial-place/grave-site/{id}/clarify-geo-position',
        name: 'admin_grave_site_clarify_geo_position',
        methods: HttpRequest::METHOD_PATCH
    )]
    public function clarifyGraveSiteGeoPosition(HttpRequest $httpRequest, string $id): HttpJsonResponse
    {
        $this->assertValidCsrfToken($httpRequest, 'grave_site');
        $commandRequest  = $this->handleJsonRequest($httpRequest, ClarifyGraveSiteGeoPositionRequest::class);
        $commandResponse = $this->appRequestBus->execute($commandRequest);

        return $this->buildJsonResponse($commandResponse, HttpResponse::HTTP_OK);
    }

    #[Route(
        '/admin/burial-place/grave-site/{id}/replace-person-in-charge',
        name: 'admin_grave_site_replace_person_in_charge',
        methods: HttpRequest::METHOD_PATCH
    )]
    public function replaceGraveSitePersonInCharge(HttpRequest $httpRequest, string $id): HttpJsonResponse
    {
        $this->assertValidCsrfToken($httpRequest, 'grave_site');
        $commandRequest  = $this->handleJsonRequest($httpRequest, ReplaceGraveSitePersonInChargeRequest::class);
        $commandResponse = $this->appRequestBus->execute($commandRequest);

        return $this->buildJsonResponse($commandResponse, HttpResponse::HTTP_OK);
    }

    #[Route(
        '/admin/burial-place/grave-site/{id}/clear-size',
        name: 'admin_grave_site_clear_size',
        methods: HttpRequest::METHOD_PATCH
    )]
    public function clearSize(HttpRequest $httpRequest, string $id): HttpJsonResponse
    {
        $this->assertValidCsrfToken($httpRequest, 'grave_site');        // TODO extract token validation to base class
        $commandRequest  = $this->handleJsonRequest($httpRequest, ClearGraveSiteSizeRequest::class);
        $commandResponse = $this->appRequestBus->execute($commandRequest);

        return $this->buildJsonResponse($commandResponse, HttpResponse::HTTP_OK);
    }

    #[Route(
        '/admin/burial-place/grave-site/{id}/clear-geo-position',
        name: 'admin_grave_site_clear_geo_position',
        methods: HttpRequest::METHOD_PATCH
    )]
    public function clearGeoPosition(HttpRequest $httpRequest, string $id): HttpJsonResponse
    {
        $this->assertValidCsrfToken($httpRequest, 'grave_site');        // TODO extract token validation to base class
        $commandRequest  = $this->handleJsonRequest($httpRequest, ClearGraveSiteGeoPositionRequest::class);
        $commandResponse = $this->appRequestBus->execute($commandRequest);

        return $this->buildJsonResponse($commandResponse, HttpResponse::HTTP_OK);
    }

    #[Route(
        '/admin/burial-place/grave-site/{id}/discard-person-in-charge',
        name: 'admin_grave_site_discard_person_in_charge',
        methods: HttpRequest::METHOD_PATCH
    )]
    public function discardPersonInCharge(HttpRequest $httpRequest, string $id): HttpJsonResponse
    {
        $this->assertValidCsrfToken($httpRequest, 'grave_site');        // TODO extract token validation to base class
        $commandRequest  = $this->handleJsonRequest($httpRequest, DiscardGraveSitePersonInChargeRequest::class);
        $commandResponse = $this->appRequestBus->execute($commandRequest);

        return $this->buildJsonResponse($commandResponse, HttpResponse::HTTP_OK);
    }

    #[Route(
        '/admin/burial-place/grave-site/{id}',
        name: 'admin_grave_site_remove',
        methods: HttpRequest::METHOD_DELETE
    )]
    public function removeGraveSite(HttpRequest $httpRequest): HttpJsonResponse
    {
        $this->assertValidCsrfToken($httpRequest, 'grave_site');
        $commandRequest  = $this->handleJsonRequest($httpRequest, RemoveGraveSiteRequest::class);
        $commandResponse = $this->appRequestBus->execute($commandRequest);

        return $this->buildJsonResponse($commandResponse, HttpResponse::HTTP_NO_CONTENT);
    }
}
