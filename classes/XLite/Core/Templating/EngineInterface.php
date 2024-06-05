<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Templating;

use XLite\View\AView;

interface EngineInterface
{
    /**
     * Outputs a rendered template
     *
     * @param string $templateName Name of template to render
     * @param AView  $thisObject   Object that will be set as "this" context var
     * @param array  $parameters   Optional context vars
     *
     * @return string
     */
    public function display($templateName, AView $thisObject, array $parameters = []);
}
