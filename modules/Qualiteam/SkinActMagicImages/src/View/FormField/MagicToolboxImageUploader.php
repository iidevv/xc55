<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// vim: set ts=4 sw=4 sts=4 et:

namespace Qualiteam\SkinActMagicImages\View\FormField;

use Qualiteam\SkinActMagicImages\Model\MagicSwatchesSet;
use XLite\Model\WidgetParam\TypeObject;

/**
 * Image file uploader
 */
class MagicToolboxImageUploader extends \XLite\View\FormField\FileUploader\Image
{
    public const PARAM_SET = 'set';

    protected function defineWidgetParams(): void
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_SET => new TypeObject(
                'Set',
                null,
                false,
                MagicSwatchesSet::class
            ),
        ];
    }

    protected function getSetId()
    {
        return $this->getSet() ? $this->getSet()->getId() : null;
    }

    protected function getSet()
    {
        return $this->getParam(static::PARAM_SET);
    }

    /**
     * getCommonAttributes
     *
     * @return array
     */
    protected function getCommonAttributes(): array
    {
        $list = parent::getCommonAttributes();
        $list['magic_swatches_set'] = $this->getSetId();
        return $list;
    }
}