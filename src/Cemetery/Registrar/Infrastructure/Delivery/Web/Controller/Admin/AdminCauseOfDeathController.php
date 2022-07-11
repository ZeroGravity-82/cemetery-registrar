<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Delivery\Web\Controller\Admin;

use Cemetery\Registrar\Application\Command\CauseOfDeath\CreateCauseOfDeath\CreateCauseOfDeathRequest;
use Cemetery\Registrar\Application\Command\CauseOfDeath\CreateCauseOfDeath\CreateCauseOfDeathService;
use Cemetery\Registrar\Application\Command\CauseOfDeath\EditCauseOfDeath\EditCauseOfDeathRequest;
use Cemetery\Registrar\Application\Command\CauseOfDeath\EditCauseOfDeath\EditCauseOfDeathService;
use Cemetery\Registrar\Application\Command\CauseOfDeath\RemoveCauseOfDeath\RemoveCauseOfDeathRequest;
use Cemetery\Registrar\Application\Command\CauseOfDeath\RemoveCauseOfDeath\RemoveCauseOfDeathService;
use Cemetery\Registrar\Application\Query\CauseOfDeath\CountCauseOfDeathTotal\CountCauseOfDeathTotalRequest;
use Cemetery\Registrar\Application\Query\CauseOfDeath\CountCauseOfDeathTotal\CountCauseOfDeathTotalService;
use Cemetery\Registrar\Application\Query\CauseOfDeath\ListCausesOfDeath\ListCausesOfDeathRequest;
use Cemetery\Registrar\Application\Query\CauseOfDeath\ListCausesOfDeath\ListCausesOfDeathService;
use Cemetery\Registrar\Domain\View\CauseOfDeath\CauseOfDeathFetcher;
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
        private readonly CreateCauseOfDeathService     $createCauseOfDeathService,
        private readonly EditCauseOfDeathService       $editCauseOfDeathService,
        private readonly RemoveCauseOfDeathService     $removeCauseOfDeathService,
        private readonly CauseOfDeathFetcher           $causeOfDeathFetcher,
    ) {}

    #[Route('/admin/cause-of-death', name: 'admin_cause_of_death_list', methods: Request::METHOD_GET)]
    public function list(): Response
    {
        $causeOfDeathTotalCount = $this->countCauseOfDeathTotalService
            ->execute(new CountCauseOfDeathTotalRequest())
            ->totalCount;
        $causeOfDeathList = $this->listCausesOfDeathService
            ->execute(new ListCausesOfDeathRequest())
            ->list;

        return $this->render('admin/cause_of_death/list.html.twig', [
            'causeOfDeathTotalCount' => $causeOfDeathTotalCount,
            'causeOfDeathList'       => $causeOfDeathList,
        ]);
    }

    #[Route('/admin/cause-of-death/new', name: 'admin_cause_of_death_new', methods: Request::METHOD_POST)]
    public function new(Request $request): JsonResponse
    {
        $createRequest = $this->handleJsonRequest($request, CreateCauseOfDeathRequest::class);
        $id            = $this->createCauseOfDeathService->execute($createRequest)->id;
        $view          = $this->causeOfDeathFetcher->getViewById($id);

        return $this->json($view, Response::HTTP_CREATED);
    }

    #[Route('/admin/cause-of-death/edit/{id}', name: 'admin_cause_of_death_edit', methods: [
        Request::METHOD_GET,
        Request::METHOD_PUT,
    ])]
    public function edit(Request $request, string $id): JsonResponse
    {
        if ($request->isMethod(Request::METHOD_PUT)) {
            $editRequest = $this->handleJsonRequest($request, EditCauseOfDeathRequest::class);
            $id          = $this->editCauseOfDeathService->execute($editRequest)->id;
        }
        $view = $this->causeOfDeathFetcher->getViewById($id);

        return $this->json($view);
    }

    #[Route('/admin/cause-of-death/{id}', name: 'admin_cause_of_death_remove', methods: Request::METHOD_DELETE)]
    public function remove(Request $request, string $id): JsonResponse
    {
        $removeRequest = $this->handleJsonRequest($request, RemoveCauseOfDeathRequest::class);
        $this->removeCauseOfDeathService->execute($removeRequest);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
