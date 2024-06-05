<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActImagesForColors\View\FormField\Select;

/**
 * Swatch
 */
class SwatchInline extends \XLite\View\FormField\Inline\Base\Single
{
    /**
     * @inheritdoc
     */
    protected function defineFieldClass()
    {
        return 'Qualiteam\SkinActImagesForColors\View\FormField\Select\Swatch';
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
