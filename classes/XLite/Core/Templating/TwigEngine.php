<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Templating;

use Twig\Environment;
use XLite\View\AView;

class TwigEngine implements EngineInterface
{
    private Environment $twig;

    public function __construct(
        Environment $twig
    ) {
        $this->twig = $twig;
    }

    /**
     * Outputs a rendered template
     *
     * @param string $templateName Name of template to render
     * @param AView  $thisObject   Object that will be set as "this" context var
     * @param array  $parameters   Optional context vars
     *
     * @return string
     */
    public function display($templateName, AView $thisObject, array $parameters = [])
    {
        $globals = $this->twig->getGlobals();
        $oldThis = array_key_exists('this', $globals) ? $globals['this'] : null;

        $this->twig->addGlobal('this', $thisObject);

        $this->twig->display($templateName, $parameters + ['this' => $this]);

        $this->twig->addGlobal('this', $oldThis);
    }
}
