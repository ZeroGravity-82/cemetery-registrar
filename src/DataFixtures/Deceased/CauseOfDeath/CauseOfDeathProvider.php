<?php

declare(strict_types=1);

namespace DataFixtures\Deceased\CauseOfDeath;

use Cemetery\Registrar\Domain\Model\Deceased\CauseOfDeath\CauseOfDeath;
use Cemetery\Registrar\Domain\Model\Deceased\CauseOfDeath\CauseOfDeathDescription;
use Cemetery\Registrar\Domain\Model\Deceased\CauseOfDeath\CauseOfDeathId;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CauseOfDeathProvider
{
    public static function getCauseOfDeathA(): CauseOfDeath
    {
        $id          = new CauseOfDeathId('CD001');
        $description = new CauseOfDeathDescription('COVID-19');

        return new CauseOfDeath($id, $description);
    }

    public static function getCauseOfDeathB(): CauseOfDeath
    {
        $id          = new CauseOfDeathId('CD002');
        $description = new CauseOfDeathDescription('Обструктивная болезнь легких');

        return new CauseOfDeath($id, $description);
    }

    public static function getCauseOfDeathC(): CauseOfDeath
    {
        $id          = new CauseOfDeathId('CD003');
        $description = new CauseOfDeathDescription('Атеросклеротическая болезнь сердца');

        return new CauseOfDeath($id, $description);
    }

    public static function getCauseOfDeathD(): CauseOfDeath
    {
        $id          = new CauseOfDeathId('CD004');
        $description = new CauseOfDeathDescription('Онкология');

        return new CauseOfDeath($id, $description);
    }

    public static function getCauseOfDeathE(): CauseOfDeath
    {
        $id          = new CauseOfDeathId('CD005');
        $description = new CauseOfDeathDescription('Астма кардиальная');

        return new CauseOfDeath($id, $description);
    }

    public static function getCauseOfDeathF(): CauseOfDeath
    {
        $id          = new CauseOfDeathId('CD006');
        $description = new CauseOfDeathDescription('Асфиксия');

        return new CauseOfDeath($id, $description);
    }

    public static function getCauseOfDeathG(): CauseOfDeath
    {
        $id          = new CauseOfDeathId('CD007');
        $description = new CauseOfDeathDescription('Аневризма брюшной аорты разорванная');

        return new CauseOfDeath($id, $description);
    }

    public static function getCauseOfDeathH(): CauseOfDeath
    {
        $id          = new CauseOfDeathId('CD008');
        $description = new CauseOfDeathDescription('Болезнь сердечно-легочная хроническая');

        return new CauseOfDeath($id, $description);
    }
}
