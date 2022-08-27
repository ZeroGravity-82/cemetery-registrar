<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Delivery\Web\Controller\Admin\NaturalPerson;

use Cemetery\Registrar\Application\ApplicationRequestBus;
use Cemetery\Registrar\Application\NaturalPerson\Query\ListNaturalPersons\ListNaturalPersonsRequest;
use Cemetery\Registrar\Infrastructure\Delivery\Web\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class AdminNaturalPersonController extends Controller
{
    public function __construct(
        private readonly ApplicationRequestBus $appRequestBus,
    ) {}

    #[Route('/admin/natural-person', name: 'admin_natural_person_list', methods: 'GET')]
    public function list(): Response
    {
        $queryRequest  = new ListNaturalPersonsRequest();
        $queryResponse = $this->appRequestBus->execute($queryRequest);

        $list       = $queryResponse->data->list;
        $totalCount = $queryResponse->data->totalCount;

        return $this->render('admin/natural_person/list_natural_person.html.twig', [
            'list'       => $list,
            'totalCount' => $totalCount,
        ]);
    }
}
