<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Delivery\Web\Controller\Admin;

use Cemetery\Registrar\Application\Command\CauseOfDeath\CreateCauseOfDeath\CreateCauseOfDeathRequest;
use Cemetery\Registrar\Application\Command\CauseOfDeath\CreateCauseOfDeath\CreateCauseOfDeathService;
use Cemetery\Registrar\Application\Query\CauseOfDeath\CountCauseOfDeathTotal\CountCauseOfDeathTotalRequest;
use Cemetery\Registrar\Application\Query\CauseOfDeath\CountCauseOfDeathTotal\CountCauseOfDeathTotalService;
use Cemetery\Registrar\Application\Query\CauseOfDeath\ListCausesOfDeath\ListCausesOfDeathRequest;
use Cemetery\Registrar\Application\Query\CauseOfDeath\ListCausesOfDeath\ListCausesOfDeathService;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathId;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathRepository;
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
    /**
     * @param CountCauseOfDeathTotalService $countCauseOfDeathTotalService
     * @param ListCausesOfDeathService      $listCausesOfDeathService
     * @param CreateCauseOfDeathService     $createCauseOfDeathService
     * @param CauseOfDeathRepository        $causeOfDeathRepo
     * @param CauseOfDeathFetcher           $causeOfDeathFetcher
     */
    public function __construct(
        private readonly CountCauseOfDeathTotalService $countCauseOfDeathTotalService,
        private readonly ListCausesOfDeathService      $listCausesOfDeathService,
        private readonly CreateCauseOfDeathService     $createCauseOfDeathService,
        private readonly CauseOfDeathRepository        $causeOfDeathRepo,
        private readonly CauseOfDeathFetcher           $causeOfDeathFetcher,
    ) {}

    /**
     * @return Response
     */
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

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    #[Route('/admin/cause-of-death/new', name: 'admin_cause_of_death_new', methods: Request::METHOD_POST)]
    public function new(Request $request): Response
    {
        $name                      = $this->getInputString($request, 'name');
        $createCauseOfDeathRequest = new CreateCauseOfDeathRequest($name);
        $causeOfDeathId            = $this->createCauseOfDeathService
            ->execute($createCauseOfDeathRequest)->causeOfDeathId;

        return $this->json(['id' => $causeOfDeathId], Response::HTTP_CREATED);
    }

    /**
     * @param Request $request
     * @param string  $id
     *
     * @return JsonResponse
     */
    #[Route('/admin/cause-of-death/edit/{id}', name: 'admin_cause_of_death_edit', methods: [
        Request::METHOD_GET,
        Request::METHOD_PATCH,
    ])]
    public function edit(Request $request, string $id): JsonResponse
    {
        if ($request->isMethod(Request::METHOD_PATCH)) {
            $causeOfDeath = $this->causeOfDeathRepo->findById(new CauseOfDeathId($id));
        }

        if ($request->isMethod(Request::METHOD_GET)) {
            $burialView = $this->causeOfDeathFetcher->getViewById($id);

            return $this->json($burialView);
        }

        return $response;
    }
}
