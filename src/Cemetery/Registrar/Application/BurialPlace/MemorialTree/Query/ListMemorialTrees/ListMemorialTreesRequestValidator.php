<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\MemorialTree\Query\ListMemorialTrees;

use Cemetery\Registrar\Application\Notification;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListMemorialTreesRequestValidator
{
    public function validate(ListMemorialTreesRequest $request): Notification
    {
        // TODO add validation
        return new Notification();
    }
}
