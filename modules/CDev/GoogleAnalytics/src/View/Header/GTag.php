<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\View\Header;

use XLite\Model\WidgetParam\TypeCollection;

/**
 * Header declaration (gtag.js)
 */
class GTag extends AHeaderTag
{
    public const PARAM_GTAG_OPTIONS = 'gtag_options';

    /** @noinspection ReturnTypeCanBeDeclaredInspection */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_GTAG_OPTIONS => new TypeCollection('Options', []),
        ];
    }

    /**
     * Return widget default template
     *
     * @return string
     * @noinspection PhpMissingReturnTypeInspection
     * @noinspection ReturnTypeCanBeDeclaredInspection
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/GoogleAnalytics/header/gtag.twig';
    }
}
