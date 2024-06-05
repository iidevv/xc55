<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\Core\Templating\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use XC\ThemeTweaker\Core\Templating\Twig\Functions;
use XC\ThemeTweaker\Core\Templating\Twig\TokenParser\XCartInclude;

class ThemeTweakerExtension extends AbstractExtension
{
    private Functions $functions;

    public function __construct(
        Functions $functions
    ) {
        $this->functions = $functions;
    }

    public function getTokenParsers(): array
    {
        return [new XCartInclude()];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'include',
                [$this->functions, 'xcart_include'],
                [
                    'needs_environment' => true,
                    'needs_context'     => true,
                    'is_safe'           => ['all'],
                ]
            ),
        ];
    }
}
