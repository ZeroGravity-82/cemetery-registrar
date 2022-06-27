<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\Deceased;

use Cemetery\Registrar\Domain\Model\Deceased\CremationCertificateId;
use Cemetery\Tests\Registrar\Domain\Model\EntityIdTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CremationCertificateIdTest extends EntityIdTest
{
    protected string $className = CremationCertificateId::class;
}
