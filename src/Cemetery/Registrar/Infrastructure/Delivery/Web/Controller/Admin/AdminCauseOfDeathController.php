<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Delivery\Web\Controller\Admin;

use Cemetery\Registrar\Application\Query\CauseOfDeath\CountCauseOfDeathTotal\CountCauseOfDeathTotalRequest;
use Cemetery\Registrar\Application\Query\CauseOfDeath\CountCauseOfDeathTotal\CountCauseOfDeathTotalService;
use Cemetery\Registrar\Application\Query\CauseOfDeath\ListCausesOfDeath\ListCausesOfDeathRequest;
use Cemetery\Registrar\Application\Query\CauseOfDeath\ListCausesOfDeath\ListCausesOfDeathService;
use Cemetery\Registrar\Infrastructure\Delivery\Web\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
#[Route('/admin/cause-of-death')]
class AdminCauseOfDeathController extends Controller
{
    /**
     * @param CountCauseOfDeathTotalService $countCauseOfDeathTotalService
     * @param ListCausesOfDeathService      $listCausesOfDeathService
     */
    public function __construct(
        private readonly CountCauseOfDeathTotalService $countCauseOfDeathTotalService,
        private readonly ListCausesOfDeathService      $listCausesOfDeathService,
    ) {}

    #[Route('/', name: 'admin_cause_of_death_list', methods: 'GET')]
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
}
