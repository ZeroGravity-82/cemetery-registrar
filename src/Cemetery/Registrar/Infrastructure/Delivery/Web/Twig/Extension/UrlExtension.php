<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Delivery\Web\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class UrlExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('url_decode', [$this, 'urlDecode']),
        ];
    }

    /**
     * @param string $value
     *
     * @return string
     */
    public function urlDecode(string $value): string
    {
        return \urldecode($value);
    }
}
