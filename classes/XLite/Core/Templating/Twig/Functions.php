<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Templating\Twig;

use Symfony\Component\Asset\Packages;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;
use XLite\Core\Layout;
use XLite\Core\Translation;

/**
 * Custom twig functions
 *
 * TODO: Move widget instantiation logic from AView to a separate WidgetFactory
 */
class Functions
{
    private Layout $layout;

    private UrlGeneratorInterface $generator;

    private Packages $packages;

    public function __construct(
        Layout $layout,
        UrlGeneratorInterface $generator,
        Packages $packages
    ) {
        $this->layout    = $layout;
        $this->generator = $generator;
        $this->packages  = $packages;
    }

    public function widget(Environment $env, array $arguments = [])
    {
        $nextPositionalArgument = 0;

        $class = null;

        if (isset($arguments[$nextPositionalArgument]) && is_string($arguments[$nextPositionalArgument])) {
            $class = $arguments[$nextPositionalArgument];
            unset($arguments[$nextPositionalArgument]);
            $nextPositionalArgument++;
        }

        /** @var \XLite\View\AView $widget */
        $widget = $env->getGlobals()['this'];

        return $widget->getWidget(
            $arguments[$nextPositionalArgument] ?? $arguments,
            $class
        );
    }

    public function widgetList(Environment $env, array $arguments = [])
    {
        $type = isset($arguments['type']) ? strtolower($arguments['type']) : null;

        unset($arguments['type']);

        $name = $arguments[0];

        unset($arguments[0]);

        if (isset($arguments[1])) {
            // Instantiate widget list with parameters passed in the second positional argument ($arguments[1])

            $params = $arguments[1];
        } else {
            $params = $arguments;
        }

        /** @var \XLite\View\AView $widget */
        $widget = $env->getGlobals()['this'];
        if ($type === 'inherited') {
            $widget->displayInheritedViewListContent($name, $params);
        } elseif ($type === 'nested') {
            $widget->displayNestedViewListContent($name, $params);
        } else {
            $widget->displayViewListContent($name, $params);
        }
    }

    public function t($name, array $arguments = [], $code = null, $type = null)
    {
        return Translation::lbl($name, $arguments, $code, $type);
    }

    public function svg(Environment $env, $path, $interface = null)
    {
        return $env->getGlobals()['this']->getSVGImage($path, $interface);
    }

    public function url(Environment $env, ...$args): string
    {
        if ((!isset($args[1]) || is_string($args[1])) && $widget = ($env->getGlobals()['this'] ?? null)) {
            trigger_deprecation('xcart', '5.5.0.0', 'Usage of twig function url() for XCart routing is deprecated, use xurl or symfony routing instead');

            return $widget->buildURL(...$args);
        }

        return $this->generator->generate(...$args);
    }

    public function xurl(Environment $env, ...$args): string
    {
        if ($widget = ($env->getGlobals()['this'] ?? null)) {
            return $widget->buildURL(...$args);
        }

        return '';
    }

    public function asset(Environment $env, ...$args)
    {
        if ($env->getGlobals()['this'] ?? null) {
            return $this->layout->getResourceWebPath($args[0], Layout::WEB_PATH_OUTPUT_URL);
        }

        return $this->packages->getUrl(...$args);
    }
}
