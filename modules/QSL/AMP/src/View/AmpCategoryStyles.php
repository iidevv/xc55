<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AMP\View;

use XCart\Extender\Mapping\ListChild;

/**
 * AMP category page styles
 *
 * @ListChild (list="amp.center", weight="0")
 */
class AmpCategoryStyles extends \XLite\View\AView
{
    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return ['category', 'main'];
    }

    /**
     * AMP-mode styles
     *
     * NOTE: Use this method instead of getCSSFiles for AMP page styles
     *
     * .less files are merged with modules/QSL/AMP/styles/initialize.less by default
     *
     * @return array
     */
    protected function getAmpCSSFiles()
    {
        return [
            [
                'file' => 'modules/QSL/AMP/styles/category.less',
            ],
        ];
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return null;
    }
}
