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
        $id   = new CemeteryBlockId('GB001');
        $name = new CemeteryBlockName('воинский квартал');

        return new CemeteryBlock($id, $name);
    }

    public static function getCemeteryBlockB(): CemeteryBlock
    {
        $id   = new CemeteryBlockId('GB002');
        $name = new CemeteryBlockName('общий квартал А');

        return new CemeteryBlock($id, $name);
    }

    public static function getCemeteryBlockC(): CemeteryBlock
    {
        $id   = new CemeteryBlockId('GB003');
        $name = new CemeteryBlockName('общий квартал Б');

        return new CemeteryBlock($id, $name);
    }

    public static function getCemeteryBlockD(): CemeteryBlock
    {
        $id   = new CemeteryBlockId('GB004');
        $name = new CemeteryBlockName('мусульманский квартал');

        return new CemeteryBlock($id, $name);
    }
}
