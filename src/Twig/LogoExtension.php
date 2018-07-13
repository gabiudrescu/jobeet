<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class LogoExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('displayLogoPath', [$this, 'displayLogo'], ['is_safe' => ['html']]),
        ];
    }

    public function displayLogo($logoPath)
    {
        if(0 === (stripos($logoPath, 'http')))
        {
            return $logoPath;
        };

        return sprintf('/images/logo/%s', $logoPath);
    }
}
