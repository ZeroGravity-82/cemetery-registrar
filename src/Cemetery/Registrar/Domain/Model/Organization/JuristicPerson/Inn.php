<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Organization\JuristicPerson;

use Cemetery\Registrar\Domain\Model\Organization\AbstractInn;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class Inn extends AbstractInn
{
    private const INN_LENGTH = 10;

    /**
     * {@inheritdoc}
     */
    protected function innLength(): int
    {
        return self::INN_LENGTH;
    }

    /**
     * {@inheritdoc}
     */
    protected function assertValidCheckDigits(string $value): void
    {
        $checkDigit1 = (int) $value[$this->innLength() - 1];
        $checkValue1 = $this->calculateCheckDigit($value, [2, 4, 10, 3, 5, 9, 4, 6, 8]);
        if ($checkDigit1 !== $checkValue1) {
            $this->throwInvalidCheckDigitsException();
        }
    }
}
