<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\BurialPlace\MemorialTree;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class MemorialTreeNumber
{
    /**
     * @param string $value
     */
    public function __construct(
        private readonly string $value,
    ) {
        $this->assertValidValue($value);
    }

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
     * @param self $treeNumber
     *
     * @return bool
     */
    public function isEqual(self $treeNumber): bool
    {
        return $treeNumber->value() === $this->value();
    }

    /**
     * @param string $value
     */
    private function assertValidValue(string $value): void
    {
        $this->assertNotEmpty($value);
    }

    /**
     * @param string $value
     *
     * @throws \InvalidArgumentException when the tree number is an empty string
     */
    private function assertNotEmpty(string $value): void
    {
        if (\trim($value) === '') {
            throw new \InvalidArgumentException('Номер мемориального дерева не может иметь пустое значение.');
        }
    }
}
