<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\NaturalPerson\Command\DiscardNaturalPersonDeceasedDetails;

use Cemetery\Registrar\Application\AbstractApplicationRequest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DiscardNaturalPersonDeceasedDetailsRequest extends AbstractApplicationRequest
{
    public function __construct(
        public ?string $id,
    ) {}
}
