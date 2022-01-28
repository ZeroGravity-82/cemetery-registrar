<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Cemetery\Registrar\Domain\Deceased\DeceasedId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types\DeceasedIdType;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DeceasedIdTypeTest extends AbstractStringTypeTest
{
    protected string $className = DeceasedIdType::class;

    protected string $typeName = 'deceased_id';

    public function setUp(): void
    {
        parent::setUp();

        $this->dbValue  = '84c6e97b-7274-4335-a426-d78abd74e909';
        $this->phpValue = new DeceasedId('84c6e97b-7274-4335-a426-d78abd74e909');
    }
}
