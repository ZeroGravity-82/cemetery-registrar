<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\ColumbariumNiche\Query\ListColumbariumNiches;

use Cemetery\Registrar\Application\Notification;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListColumbariumNichesRequestValidator
{
    public function validate(ListColumbariumNichesRequest $request): Notification
    {
        // TODO add validation
        return new Notification();
    }
}
