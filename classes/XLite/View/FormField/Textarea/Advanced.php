<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Textarea;

/**
 * Textarea
 */
class Advanced extends \XLite\View\FormField\Textarea\Simple
{
    /**
     * Widget param name
     */
    public const PARAM_STYLE = 'style';

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_STYLE => new \XLite\Model\WidgetParam\TypeString('Style', $this->getDefaultStyle()),
        ];
    }

    /**
     * Define the size of the button.
     *
     * @return string
     */
    protected function getDefaultStyle()
    {
        return '';
    }
}
