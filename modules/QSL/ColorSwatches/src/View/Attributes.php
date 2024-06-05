<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ColorSwatches\View;

use XCart\Extender\Mapping\Extender;

/**
 * Attributes page view
 * @Extender\Mixin
 */
class Attributes extends \XLite\View\Attributes
{
    /**
     * @inheritdoc
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/QSL/ColorSwatches/attributes/style.css';
        $list[] = 'modules/QSL/ColorSwatches/form_field/swatch.css';

        return $list;
    }

    /**
     * @inheritdoc
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/QSL/ColorSwatches/attributes/script.js';
        $list[] = 'modules/QSL/ColorSwatches/form_field/swatch.js';

        return $list;
    }
}
