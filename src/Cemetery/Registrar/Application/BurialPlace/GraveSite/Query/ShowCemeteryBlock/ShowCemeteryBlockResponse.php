<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Query\ShowCemeteryBlock;

use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Domain\View\BurialPlace\GraveSite\CemeteryBlockView;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ShowCemeteryBlockResponse extends ApplicationSuccessResponse
{
    public function __construct(
        CemeteryBlockView $view,
    ) {
        $this->data = (object) [
            'view' => $view,
        ];
    }
}
