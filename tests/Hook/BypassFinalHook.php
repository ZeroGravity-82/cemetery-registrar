<?php

declare(strict_types=1);

namespace Hook;

use DG\BypassFinals;
use PHPUnit\Runner\BeforeTestHook;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class BypassFinalHook implements BeforeTestHook
{
    /**
     * Enables Bypass Finals package.
     * @link https://github.com/dg/bypass-finals
     *
     * @param string $test
     */
    public function executeBeforeTest(string $test): void
    {
        BypassFinals::enable();
    }
}
