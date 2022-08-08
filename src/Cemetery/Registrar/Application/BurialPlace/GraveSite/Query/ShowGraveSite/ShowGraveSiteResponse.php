<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Query\ShowGraveSite;

use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Domain\View\BurialPlace\GraveSite\GraveSiteView;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ShowGraveSiteResponse extends ApplicationSuccessResponse
{
    public function __construct(
        GraveSiteView $view,
    ) {
        $this->data = (object) [
            'view' => $view,
        ];
    }
}
