<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ColorSwatches\View\FormField\Inline\Input\Text;

/**
 * Color
 */
class Color extends \XLite\View\FormField\Inline\Input\Text\Color
{
    /**
     * @inheritdoc
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $key = array_search('form_field/inline/input/text/color.js', $list, true);
        if ($key !== false) {
            unset($list[$key]);
        }

        $list[] = 'modules/QSL/ColorSwatches/form_field/inline/input/text/color.js';

        return $list;
    }

    /**
     * @inheritdoc
     */
    protected function getFieldTemplate()
    {
        return 'modules/QSL/ColorSwatches/form_field/inline/input/text/field.twig';
    }
}
