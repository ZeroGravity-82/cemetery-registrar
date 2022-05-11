<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
interface ApplicationService
{
    /**
     * @param $request
     *
     * @return mixed|void
     */
    public function execute($request);
}
