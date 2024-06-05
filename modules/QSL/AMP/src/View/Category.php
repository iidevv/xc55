<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AMP\View;

use XCart\Extender\Mapping\ListChild;
use QSL\AMP\Core\HtmlToAmpConverter;

/**
 * Amp version of XLite\View\Category
 *
 * @ListChild (list="amp.center", weight="10")
 */
class Category extends \XLite\View\Category
{
    /**
     * Get AMP compatible category description
     *
     * @return string
     */
    protected function getDescription()
    {
        $description = parent::getDescription();

        $converter = HtmlToAmpConverter::getInstance();

        return $converter->convert($description);
    }

    /**
     * Amp components
     *
     * @return array
     */
    protected function getAmpComponents()
    {
        $components = parent::getAmpComponents();

        $description = $this->getDescription();

        if (strpos($description, 'amp-iframe') !== false) {
            $components[] = 'amp-iframe';
        }

        if (strpos($description, 'amp-youtube') !== false) {
            $components[] = 'amp-youtube';
        }

        if (strpos($description, 'amp-vimeo') !== false) {
            $components[] = 'amp-vimeo';
        }

        if (strpos($description, 'amp-video') !== false) {
            $components[] = 'amp-video';
        }

        return $components;
    }
}
