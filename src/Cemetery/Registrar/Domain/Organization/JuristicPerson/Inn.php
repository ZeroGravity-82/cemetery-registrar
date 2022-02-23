<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Organization\JuristicPerson;

use Cemetery\Registrar\Domain\Organization\AbstractInn;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class Inn extends AbstractInn
{
    private const INN_LENGTH = 10;

    /**
     * {@inheritdoc}
     */
    protected function getInnLength(): int
    {
        return self::INN_LENGTH;
    }

    /**
     * {@inheritdoc}
     */
    protected function assertValidCheckDigits(string $value): void
    {
        $checkDigit1 = (int) $value[$this->getInnLength() - 1];
        $checkValue1 = $this->calculateCheckDigit($value, [2, 4, 10, 3, 5, 9, 4, 6, 8]);
        if ($checkDigit1 !== $checkValue1) {
            $this->throwIncorrectCheckDigitsException();
        }
    }
}
