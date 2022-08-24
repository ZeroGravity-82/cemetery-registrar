<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Delivery\Web\Controller\Admin\BurialPlace;

use Cemetery\Registrar\Application\ApplicationRequestBus;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\CreateCemeteryBlock\CreateCemeteryBlockRequest;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\CreateGraveSite\CreateGraveSiteRequest;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\EditCemeteryBlock\EditCemeteryBlockRequest;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\EditGraveSite\EditGraveSiteRequest;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\RemoveCemeteryBlock\RemoveCemeteryBlockRequest;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\RemoveGraveSite\RemoveGraveSiteRequest;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\Query\ListGraveSites\ListGraveSitesRequest;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\Query\ShowCemeteryBlock\ShowCemeteryBlockRequest;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\Query\ShowGraveSite\ShowGraveSiteRequest;
use Cemetery\Registrar\Infrastructure\Delivery\Web\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse as HttpJsonResponse;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class AdminBurialPlaceGraveSiteController extends Controller
{
    public function __construct(
        private readonly ApplicationRequestBus $appRequestBus,
    ) {}

    #[Route('/admin/burial-place/grave-site', name: 'admin_burial_place_grave_site_list', methods: 'GET')]
    public function graveSiteList(): HttpResponse
    {
        $queryRequest            = new ListGraveSitesRequest();
        $queryResponse           = $this->appRequestBus->execute($queryRequest);
        $list                    = $queryResponse->data->list;
        $totalCount              = $queryResponse->data->totalCount;
        $cemeteryBlockList       = $queryResponse->data->cemeteryBlockList;
        $cemeteryBlockTotalCount = $queryResponse->data->cemeteryBlockTotalCount;

        return $this->render('admin/burial_place/grave_site/list.html.twig', [
            'list'                    => $list,
            'totalCount'              => $totalCount,
            'cemeteryBlockList'       => $cemeteryBlockList,
            'cemeteryBlockTotalCount' => $cemeteryBlockTotalCount,
        ]);
    }

    #[Route('/admin/burial-place/grave-site/{id}', name: 'admin_grave_site_show', methods: HttpRequest::METHOD_GET)]
    public function showGraveSite(HttpRequest $httpRequest): HttpJsonResponse
    {
        $queryRequest  = $this->handleJsonRequest($httpRequest, ShowGraveSiteRequest::class);
        $queryResponse = $this->appRequestBus->execute($queryRequest);

        return $this->buildJsonResponse($queryResponse, HttpResponse::HTTP_OK);
    }

    #[Route('/admin/burial-place/cemetery-block/{id}', name: 'admin_cemetery_block_show', methods: HttpRequest::METHOD_GET)]
    public function showCemeteryBlock(HttpRequest $httpRequest): HttpJsonResponse
    {
        $queryRequest  = $this->handleJsonRequest($httpRequest, ShowCemeteryBlockRequest::class);
        $queryResponse = $this->appRequestBus->execute($queryRequest);

        return $this->buildJsonResponse($queryResponse, HttpResponse::HTTP_OK);
    }

    #[Route('/admin/burial-place/grave-site/create', name: 'admin_grave_site_create', methods: HttpRequest::METHOD_POST)]
    public function createGraveSite(HttpRequest $httpRequest): HttpJsonResponse
    {
        $this->assertValidCsrfToken($httpRequest, 'grave_site');
        $commandRequest  = $this->handleJsonRequest($httpRequest, CreateGraveSiteRequest::class);
        $commandResponse = $this->appRequestBus->execute($commandRequest);

        return $this->buildJsonResponse($commandResponse, HttpResponse::HTTP_CREATED);
    }

    #[Route('/admin/burial-place/cemetery-block/create', name: 'admin_cemetery_block_create', methods: HttpRequest::METHOD_POST)]
    public function createCemeteryBlock(HttpRequest $httpRequest): HttpJsonResponse
    {
        $this->assertValidCsrfToken($httpRequest, 'cemetery_block');
        $commandRequest  = $this->handleJsonRequest($httpRequest, CreateCemeteryBlockRequest::class);
        $commandResponse = $this->appRequestBus->execute($commandRequest);

        return $this->buildJsonResponse($commandResponse, HttpResponse::HTTP_CREATED);
    }

    #[Route('/admin/burial-place/grave-site/{id}/edit', name: 'admin_grave_site_edit', methods: HttpRequest::METHOD_PUT)]
    public function editGraveSite(HttpRequest $httpRequest, string $id): HttpJsonResponse
    {
        $this->assertValidCsrfToken($httpRequest, 'grave_site');
        $commandRequest  = $this->handleJsonRequest($httpRequest, EditGraveSiteRequest::class);
        $commandResponse = $this->appRequestBus->execute($commandRequest);

        return $this->buildJsonResponse($commandResponse, HttpResponse::HTTP_OK);
    }

    #[Route('/admin/burial-place/cemetery-block/{id}/edit', name: 'admin_cemetery_block_edit', methods: HttpRequest::METHOD_PUT)]
    public function editCemeteryBlock(HttpRequest $httpRequest, string $id): HttpJsonResponse
    {
        $this->assertValidCsrfToken($httpRequest, 'cemetery_block');
        $commandRequest  = $this->handleJsonRequest($httpRequest, EditCemeteryBlockRequest::class);
        $commandResponse = $this->appRequestBus->execute($commandRequest);

        return $this->buildJsonResponse($commandResponse, HttpResponse::HTTP_OK);
    }

    #[Route('/admin/burial-place/grave-site/{id}', name: 'admin_grave_site_remove', methods: HttpRequest::METHOD_DELETE)]
    public function removeGraveSite(HttpRequest $httpRequest): HttpJsonResponse
    {
        $this->assertValidCsrfToken($httpRequest, 'grave_site');
        $commandRequest  = $this->handleJsonRequest($httpRequest, RemoveGraveSiteRequest::class);
        $commandResponse = $this->appRequestBus->execute($commandRequest);

        return $this->buildJsonResponse($commandResponse, HttpResponse::HTTP_NO_CONTENT);
    }

    #[Route('/admin/burial-place/cemetery-block/{id}', name: 'admin_cemetery_block_remove', methods: HttpRequest::METHOD_DELETE)]
    public function removeCemeteryBlock(HttpRequest $httpRequest): HttpJsonResponse
    {
        $this->assertValidCsrfToken($httpRequest, 'cemetery_block');
        $commandRequest  = $this->handleJsonRequest($httpRequest, RemoveCemeteryBlockRequest::class);
        $commandResponse = $this->appRequestBus->execute($commandRequest);

        return $this->buildJsonResponse($commandResponse, HttpResponse::HTTP_NO_CONTENT);
    }
}
