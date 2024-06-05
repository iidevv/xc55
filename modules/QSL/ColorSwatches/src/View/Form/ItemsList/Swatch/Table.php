<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ColorSwatches\View\Form\ItemsList\Swatch;

/**
 * Swatches list table form
 */
class Table extends \XLite\View\Form\ItemsList\AItemsList
{
    /**
     * @inheritdoc
     */
    protected function getDefaultTarget()
    {
        return 'color_swatches';
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultAction()
    {
        return 'update';
    }
}
