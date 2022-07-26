<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Delivery\Web\Controller\Admin;

use Cemetery\Registrar\Application\ApplicationRequestBus;
use Cemetery\Registrar\Application\Organization\Query\CountOrganizationTotal\CountOrganizationTotalRequest;
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
        $organizationTotalCount = $this->appRequestBus->execute(new CountOrganizationTotalRequest())->totalCount;
        $organizationList       = $this->appRequestBus->execute(new ListOrganizationsRequest())->list;

        return $this->render('admin/organization/list.html.twig', [
            'organizationTotalCount' => $organizationTotalCount,
            'organizationList'       => $organizationList,
        ]);
    }
}
