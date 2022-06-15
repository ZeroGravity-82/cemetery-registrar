<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Delivery\Web\Controller\Admin;

use Cemetery\Registrar\Application\Query\Organization\CountOrganizationTotal\CountOrganizationTotalRequest;
use Cemetery\Registrar\Application\Query\Organization\CountOrganizationTotal\CountOrganizationTotalService;
use Cemetery\Registrar\Application\Query\Organization\ListOrganizations\ListOrganizationsRequest;
use Cemetery\Registrar\Application\Query\Organization\ListOrganizations\ListOrganizationsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class AdminOrganizationController extends AbstractController
{
    /**
     * @param CountOrganizationTotalService $countOrganizationTotalService
     * @param ListOrganizationsService      $listOrganizationsService
     */
    public function __construct(
        private readonly CountOrganizationTotalService $countOrganizationTotalService,
        private readonly ListOrganizationsService      $listOrganizationsService,
    ) {}

    #[Route('/admin/organization', name: 'admin_organization_list', methods: 'GET')]
    public function index(): Response
    {
        $organizationTotalCount = $this->countOrganizationTotalService
            ->execute(new CountOrganizationTotalRequest())
            ->organizationTotalCount;

        $organizationList = $this->listOrganizationsService
            ->execute(new ListOrganizationsRequest())
            ->organizationList;

        return $this->render('admin/organization/list.html.twig', [
            'organizationTotalCount' => $organizationTotalCount,
            'organizationList'       => $organizationList,
        ]);
    }
}
