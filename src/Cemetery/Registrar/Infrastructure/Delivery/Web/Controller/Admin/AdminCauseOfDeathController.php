<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Delivery\Web\Controller\Admin;

use Cemetery\Registrar\Application\CauseOfDeath\Command\CreateCauseOfDeath\CreateCauseOfDeathRequest;
use Cemetery\Registrar\Application\CauseOfDeath\Command\CreateCauseOfDeath\CreateCauseOfDeathService;
use Cemetery\Registrar\Application\CauseOfDeath\Command\EditCauseOfDeath\EditCauseOfDeathRequest;
use Cemetery\Registrar\Application\CauseOfDeath\Command\EditCauseOfDeath\EditCauseOfDeathService;
use Cemetery\Registrar\Application\CauseOfDeath\Command\RemoveCauseOfDeath\RemoveCauseOfDeathRequest;
use Cemetery\Registrar\Application\CauseOfDeath\Command\RemoveCauseOfDeath\RemoveCauseOfDeathService;
use Cemetery\Registrar\Application\CauseOfDeath\Query\CountCauseOfDeathTotal\CountCauseOfDeathTotalRequest;
use Cemetery\Registrar\Application\CauseOfDeath\Query\CountCauseOfDeathTotal\CountCauseOfDeathTotalService;
use Cemetery\Registrar\Application\CauseOfDeath\Query\ListCausesOfDeath\ListCausesOfDeathRequest;
use Cemetery\Registrar\Application\CauseOfDeath\Query\ListCausesOfDeath\ListCausesOfDeathService;
use Cemetery\Registrar\Application\CauseOfDeath\Query\ShowCauseOfDeath\ShowCauseOfDeathRequest;
use Cemetery\Registrar\Application\CauseOfDeath\Query\ShowCauseOfDeath\ShowCauseOfDeathService;
use Cemetery\Registrar\Infrastructure\Delivery\Web\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class AdminCauseOfDeathController extends Controller
{
    public function __construct(
        private readonly CountCauseOfDeathTotalService $countCauseOfDeathTotalService,
        private readonly ListCausesOfDeathService      $listCausesOfDeathService,
        private readonly ShowCauseOfDeathService       $showCauseOfDeathService,
        private readonly CreateCauseOfDeathService     $createCauseOfDeathService,
        private readonly EditCauseOfDeathService       $editCauseOfDeathService,
        private readonly RemoveCauseOfDeathService     $removeCauseOfDeathService,
    ) {}

    #[Route('/admin/cause-of-death', name: 'admin_cause_of_death_list', methods: Request::METHOD_GET)]
    public function list(): Response
    {
        $totalCount = $this->countCauseOfDeathTotalService->execute(new CountCauseOfDeathTotalRequest())->totalCount;
        $list       = $this->listCausesOfDeathService->execute(new ListCausesOfDeathRequest())->list;

        return $this->render('admin/cause_of_death/list.html.twig', [
            'causeOfDeathTotalCount' => $totalCount,
            'causeOfDeathList'       => $list,
        ]);
    }

    #[Route('/admin/cause-of-death/{id}', name: 'admin_cause_of_death_show', methods: Request::METHOD_GET)]
    public function show(Request $request): JsonResponse
    {
        $queryRequest = $this->handleJsonRequest($request, ShowCauseOfDeathRequest::class);
        $view         = $this->showCauseOfDeathService->execute($queryRequest)->view;

        return $this->json($view);
    }

    #[Route('/admin/cause-of-death/create', name: 'admin_cause_of_death_create', methods: Request::METHOD_POST)]
    public function create(Request $request): JsonResponse
    {
        $this->assertValidCsrfToken($request, 'cause_of_death');
        $commandRequest  = $this->handleJsonRequest($request, CreateCauseOfDeathRequest::class);
        // TODO add try-catch for malformed JSON and return 400 error

        $commandResponse = $this->createCauseOfDeathService->execute($commandRequest);
        if (!$commandResponse->note->hasErrors()) {
            return $this->json($commandResponse->note->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $queryRequest  = new ShowCauseOfDeathRequest($commandResponse->id);
        $queryResponse = $this->showCauseOfDeathService->execute($queryRequest);
        $response      = $this->json($queryResponse->view, Response::HTTP_CREATED);
        $locationUrl   = $this->generateUrl('admin_cause_of_death_show', ['id' => $commandResponse->id]);
        $response->headers->set('Location', $locationUrl);

        return $response;
    }

    #[Route('/admin/cause-of-death/{id}/edit', name: 'admin_cause_of_death_edit', methods: [
        Request::METHOD_GET,
        Request::METHOD_PUT,
    ])]
    public function edit(Request $request, string $id): JsonResponse
    {
        if ($request->isMethod(Request::METHOD_PUT)) {
            $this->assertValidCsrfToken($request, 'cause_of_death');
            $commandRequest = $this->handleJsonRequest($request, EditCauseOfDeathRequest::class);
            $id             = $this->editCauseOfDeathService->execute($commandRequest)->id;
        }

        $queryRequest = new ShowCauseOfDeathRequest($id);
        $view         = $this->showCauseOfDeathService->execute($queryRequest)->view;

        return $this->json($view);
    }

    #[Route('/admin/cause-of-death/{id}', name: 'admin_cause_of_death_remove', methods: Request::METHOD_DELETE)]
    public function remove(Request $request): JsonResponse
    {
        $this->assertValidCsrfToken($request, 'cause_of_death');
        $commandRequest = $this->handleJsonRequest($request, RemoveCauseOfDeathRequest::class);
        $this->removeCauseOfDeathService->execute($commandRequest);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
