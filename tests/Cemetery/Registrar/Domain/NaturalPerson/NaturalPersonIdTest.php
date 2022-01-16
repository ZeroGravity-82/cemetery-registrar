<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\NaturalPerson;

use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;
use Cemetery\Tests\Registrar\Domain\AbstractEntityIdTest;

/**
 * @author Nikolay Ryabkov <nikolay.ryabkov@sibers.com>
 */
class NaturalPersonIdTest extends AbstractEntityIdTest
{
    protected string $className = NaturalPersonId::class;
}
