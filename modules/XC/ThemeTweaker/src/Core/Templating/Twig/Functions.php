<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\Core\Templating\Twig;

use Twig\Environment;
use XLite\Core\Layout;

class Functions
{
    private Layout $layout;

    public function __construct(Layout $layout)
    {
        $this->layout = $layout;
    }

    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function xcart_include(Environment $env, $context, $template, $variables = [], $withContext = true, $ignoreMissing = false, $sandboxed = false)
    {
        /** @var \XLite\View\AView $view */
        $view = $env->getGlobals()['this'] ?? null;

        $result = '';

        if ($view) {
            $fullPath = $this->layout->getResourceFullPath($template);
            [$templateWrapperText, $templateWrapperStart] = $view->startMarker($fullPath);
            if ($templateWrapperText) {
                $result .= $templateWrapperStart;
            }
        }

        $result .= twig_include($env, $context, $template, $variables, $withContext, $ignoreMissing, $sandboxed);

        if ($view && $templateWrapperText) {
            $result .= $view->endMarker($fullPath, $templateWrapperText);
        }

        return $result;
    }
}
