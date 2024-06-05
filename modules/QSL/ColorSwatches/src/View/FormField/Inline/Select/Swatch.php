<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ColorSwatches\View\FormField\Inline\Select;

/**
 * Swatch
 */
class Swatch extends \XLite\View\FormField\Inline\Base\Single
{
    /**
     * @inheritdoc
     */
    protected function defineFieldClass()
    {
        return 'QSL\ColorSwatches\View\FormField\Select\Swatch';
    }

    /**
     * @inheritdoc
     */
    protected function hasSeparateView()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    protected function preprocessValueBeforeSave($value)
    {
        return \XLite\Core\Database::getRepo('QSL\ColorSwatches\Model\Swatch')
            ->find(parent::preprocessValueBeforeSave($value));
    }
}
