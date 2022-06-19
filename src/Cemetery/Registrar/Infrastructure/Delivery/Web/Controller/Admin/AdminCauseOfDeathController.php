<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Delivery\Web\Controller\Admin;

use Cemetery\Registrar\Application\Query\CauseOfDeath\CountCauseOfDeathTotal\CountCauseOfDeathTotalRequest;
use Cemetery\Registrar\Application\Query\CauseOfDeath\CountCauseOfDeathTotal\CountCauseOfDeathTotalService;
use Cemetery\Registrar\Application\Query\CauseOfDeath\ListCausesOfDeath\ListCausesOfDeathRequest;
use Cemetery\Registrar\Application\Query\CauseOfDeath\ListCausesOfDeath\ListCausesOfDeathService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class AdminCauseOfDeathController extends AbstractController
{
    /**
     * @param CountCauseOfDeathTotalService $countCauseOfDeathTotalService
     * @param ListCausesOfDeathService      $listCausesOfDeathService
     */
    public function __construct(
        private readonly CountCauseOfDeathTotalService $countCauseOfDeathTotalService,
        private readonly ListCausesOfDeathService      $listCausesOfDeathService,
    ) {}

    #[Route('/admin/cause-of-death', name: 'admin_cause_of_death_list', methods: 'GET')]
    public function index(): Response
    {
        $causeOfDeathTotalCount = $this->countCauseOfDeathTotalService
            ->execute(new CountCauseOfDeathTotalRequest())
            ->causeOfDeathTotalCount;

        $causeOfDeathList = $this->listCausesOfDeathService
            ->execute(new ListCausesOfDeathRequest())
            ->causeOfDeathList;

        return $this->render('admin/cause_of_death/list.html.twig', [
            'causeOfDeathTotalCount' => $causeOfDeathTotalCount,
            'causeOfDeathList'       => $causeOfDeathList,
        ]);
    }
}
