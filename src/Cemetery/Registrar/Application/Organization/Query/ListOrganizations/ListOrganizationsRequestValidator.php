<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Organization\Query\ListOrganizations;

use Cemetery\Registrar\Application\AbstractApplicationRequest;
use Cemetery\Registrar\Application\AbstractApplicationRequestValidator;
use Cemetery\Registrar\Application\Notification;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListOrganizationsRequestValidator extends AbstractApplicationRequestValidator
{
    /**
     * @param ListOrganizationsRequest $request
     */
    public function validate(AbstractApplicationRequest $request): Notification
    {
        // TODO add validation
        return new Notification();
    }
}
