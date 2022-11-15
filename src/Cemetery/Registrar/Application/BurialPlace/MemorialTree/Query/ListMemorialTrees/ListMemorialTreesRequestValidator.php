<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\MemorialTree\Query\ListMemorialTrees;

use Cemetery\Registrar\Application\AbstractApplicationRequest;
use Cemetery\Registrar\Application\AbstractApplicationRequestValidator;
use Cemetery\Registrar\Application\Notification;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListMemorialTreesRequestValidator extends AbstractApplicationRequestValidator
{
    /**
     * @param ListMemorialTreesRequest $request
     */
    public function validate(AbstractApplicationRequest $request): Notification
    {
        // TODO add validation
        return new Notification();
    }
}
