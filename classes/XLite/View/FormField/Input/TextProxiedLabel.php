<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input;

class TextProxiedLabel extends \XLite\View\FormField\Input\Text
{
    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = $this->getDir() . '/clean_urls/text_proxied_label.css';

        return $list;
    }

    public function getValue()
    {
        $value = (string) static::t(parent::getValue());

        return ($value !== parent::getValue()) ? $value : '';
    }
}
