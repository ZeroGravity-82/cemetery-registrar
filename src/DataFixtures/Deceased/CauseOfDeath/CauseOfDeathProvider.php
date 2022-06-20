<?php

declare(strict_types=1);

namespace DataFixtures\Deceased\CauseOfDeath;

use Cemetery\Registrar\Domain\Model\Deceased\CauseOfDeath\CauseOfDeath;
use Cemetery\Registrar\Domain\Model\Deceased\CauseOfDeath\CauseOfDeathName;
use Cemetery\Registrar\Domain\Model\Deceased\CauseOfDeath\CauseOfDeathId;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CauseOfDeathProvider
{
    public static function getCauseOfDeathA(): CauseOfDeath
    {
        $id   = new CauseOfDeathId('CD001');
        $name = new CauseOfDeathName('COVID-19');

        return new CauseOfDeath($id, $name);
    }

    public static function getCauseOfDeathB(): CauseOfDeath
    {
        $id   = new CauseOfDeathId('CD002');
        $name = new CauseOfDeathName('Обструктивная болезнь легких');

        return new CauseOfDeath($id, $name);
    }

    public static function getCauseOfDeathC(): CauseOfDeath
    {
        $id   = new CauseOfDeathId('CD003');
        $name = new CauseOfDeathName('Атеросклеротическая болезнь сердца');

        return new CauseOfDeath($id, $name);
    }

    public static function getCauseOfDeathD(): CauseOfDeath
    {
        $id   = new CauseOfDeathId('CD004');
        $name = new CauseOfDeathName('Онкология');

        return new CauseOfDeath($id, $name);
    }

    public static function getCauseOfDeathE(): CauseOfDeath
    {
        $id   = new CauseOfDeathId('CD005');
        $name = new CauseOfDeathName('Астма кардиальная');

        return new CauseOfDeath($id, $name);
    }

    public static function getCauseOfDeathF(): CauseOfDeath
    {
        $id   = new CauseOfDeathId('CD006');
        $name = new CauseOfDeathName('Асфиксия');

        return new CauseOfDeath($id, $name);
    }

    public static function getCauseOfDeathG(): CauseOfDeath
    {
        $id   = new CauseOfDeathId('CD007');
        $name = new CauseOfDeathName('Аневризма брюшной аорты разорванная');

        return new CauseOfDeath($id, $name);
    }

    public static function getCauseOfDeathH(): CauseOfDeath
    {
        $id   = new CauseOfDeathId('CD008');
        $name = new CauseOfDeathName('Болезнь сердечно-легочная хроническая');

        return new CauseOfDeath($id, $name);
    }
}
