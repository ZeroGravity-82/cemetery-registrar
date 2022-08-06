<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Delivery\Web\Controller\Admin;

use Cemetery\Registrar\Application\ApplicationRequestBus;
use Cemetery\Registrar\Application\Organization\Query\ListOrganizations\ListOrganizationsRequest;
use Cemetery\Registrar\Infrastructure\Delivery\Web\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class AdminOrganizationController extends Controller
{
    public function __construct(
        private readonly ApplicationRequestBus $appRequestBus,
    ) {}

    #[Route('/admin/organization', name: 'admin_organization_list', methods: 'GET')]
    public function list(): Response
    {
        $queryRequest  = new ListOrganizationsRequest();
        $queryResponse = $this->appRequestBus->execute($queryRequest);

        $organizationList       = $queryResponse->data->list;
        $organizationTotalCount = $queryResponse->data->totalCount;

        return $this->render('admin/organization/list.html.twig', [
            'list'       => $organizationList,
            'totalCount' => $organizationTotalCount,
        ]);
    }
}
