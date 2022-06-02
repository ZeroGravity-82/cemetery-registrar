<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\FuneralCompany;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class FuneralCompanyNote
{
    /**
     * @param string $value
     */
    public function __construct(
        private readonly string $value,
    ) {}

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->value();
    }

    /**
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * @param self $note
     *
     * @return bool
     */
    public function isEqual(self $note): bool
    {
        return $note->value() === $this->value();
    }
}
