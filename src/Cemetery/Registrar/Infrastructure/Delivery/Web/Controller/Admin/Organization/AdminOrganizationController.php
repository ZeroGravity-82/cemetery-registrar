<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Delivery\Web\Controller\Admin\Organization;

use Cemetery\Registrar\Application\ApplicationRequestBus;
use Cemetery\Registrar\Application\Organization\Query\ListOrganizations\ListOrganizationsRequest;
use Cemetery\Registrar\Infrastructure\Delivery\Web\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class AdminOrganizationController extends AbstractController
{
    public function __construct(
        private ApplicationRequestBus $appRequestBus,
    ) {}

    #[Route('/admin/organization', name: 'admin_organization_list', methods: 'GET')]
    public function list(): HttpResponse
    {
        $queryRequest  = new ListOrganizationsRequest();
        $queryResponse = $this->appRequestBus->execute($queryRequest);

        $organizationList       = $queryResponse->data->list;
        $organizationTotalCount = $queryResponse->data->totalCount;

        return $this->render('admin/organization/list_organization.html.twig', [
            'list'       => $organizationList,
            'totalCount' => $organizationTotalCount,
        ]);
    }
}
