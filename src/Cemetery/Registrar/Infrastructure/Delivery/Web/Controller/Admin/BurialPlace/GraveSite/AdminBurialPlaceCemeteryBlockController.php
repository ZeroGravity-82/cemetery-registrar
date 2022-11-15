<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Delivery\Web\Controller\Admin\BurialPlace\GraveSite;

use Cemetery\Registrar\Application\ApplicationRequestBus;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\CreateCemeteryBlock\CreateCemeteryBlockRequest;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\EditCemeteryBlock\EditCemeteryBlockRequest;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\RemoveCemeteryBlock\RemoveCemeteryBlockRequest;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\Query\ListCemeteryBlocks\ListCemeteryBlocksRequest;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\Query\ShowCemeteryBlock\ShowCemeteryBlockRequest;
use Cemetery\Registrar\Infrastructure\Delivery\Web\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse as HttpJsonResponse;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class AdminBurialPlaceCemeteryBlockController extends Controller
{
    public function __construct(
        private ApplicationRequestBus $appRequestBus,
    ) {}

    #[Route('/admin/burial-place/cemetery-block', name: 'admin_cemetery_block_list', methods: HttpRequest::METHOD_GET)]
    public function cemeteryBlockList(): HttpJsonResponse
    {
        $queryRequest  = new ListCemeteryBlocksRequest();
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

    #[Route('/admin/burial-place/cemetery-block/create', name: 'admin_cemetery_block_create', methods: HttpRequest::METHOD_POST)]
    public function createCemeteryBlock(HttpRequest $httpRequest): HttpJsonResponse
    {
        $this->assertValidCsrfToken($httpRequest, 'cemetery_block');
        $commandRequest  = $this->handleJsonRequest($httpRequest, CreateCemeteryBlockRequest::class);
        $commandResponse = $this->appRequestBus->execute($commandRequest);

        return $this->buildJsonResponse($commandResponse, HttpResponse::HTTP_CREATED);
    }

    #[Route('/admin/burial-place/cemetery-block/{id}/edit', name: 'admin_cemetery_block_edit', methods: HttpRequest::METHOD_PUT)]
    public function editCemeteryBlock(HttpRequest $httpRequest, string $id): HttpJsonResponse
    {
        $this->assertValidCsrfToken($httpRequest, 'cemetery_block');
        $commandRequest  = $this->handleJsonRequest($httpRequest, EditCemeteryBlockRequest::class);
        $commandResponse = $this->appRequestBus->execute($commandRequest);

        return $this->buildJsonResponse($commandResponse, HttpResponse::HTTP_OK);
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
