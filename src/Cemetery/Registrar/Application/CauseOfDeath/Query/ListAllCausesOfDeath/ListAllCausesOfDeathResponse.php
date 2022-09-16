<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CauseOfDeath\Query\ListAllCausesOfDeath;

use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Domain\View\CauseOfDeath\CauseOfDeathSimpleList;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListAllCausesOfDeathResponse extends ApplicationSuccessResponse
{
    public function __construct(
        CauseOfDeathSimpleList $list,
    ) {
        $this->data = (object) [
            'list' => $list,
        ];
    }
}
