<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\BurialPlace\GraveSite\CemeteryBlock;
use Cemetery\Registrar\Domain\BurialPlace\GraveSite\CemeteryBlockId;
use Cemetery\Registrar\Domain\BurialPlace\GraveSite\CemeteryBlockName;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class CemeteryBlockProvider
{
    public static function getCemeteryBlockA(): CemeteryBlock
    {
        $id   = new CemeteryBlockId('CB001');
        $name = new CemeteryBlockName('воинский');

        return new CemeteryBlock($id, $name);
    }

    public static function getCemeteryBlockB(): CemeteryBlock
    {
        $id   = new CemeteryBlockId('CB002');
        $name = new CemeteryBlockName('общий А');

        return new CemeteryBlock($id, $name);
    }

    public static function getCemeteryBlockC(): CemeteryBlock
    {
        $id   = new CemeteryBlockId('CB003');
        $name = new CemeteryBlockName('общий Б');

        return new CemeteryBlock($id, $name);
    }

    public static function getCemeteryBlockD(): CemeteryBlock
    {
        $id   = new CemeteryBlockId('CB004');
        $name = new CemeteryBlockName('мусульманский');

        return new CemeteryBlock($id, $name);
    }
}
