<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Deceased;

use Cemetery\Registrar\Domain\Deceased\DeathCertificateId;
use Cemetery\Tests\Registrar\Domain\AbstractEntityIdTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DeathCertificateIdTest extends AbstractEntityIdTest
{
    protected string $className = DeathCertificateId::class;
}
