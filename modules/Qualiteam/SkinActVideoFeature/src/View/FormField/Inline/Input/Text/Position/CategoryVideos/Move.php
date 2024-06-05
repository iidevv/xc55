<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoFeature\View\FormField\Inline\Input\Text\Position\CategoryVideos;

class Move extends \XLite\View\FormField\Inline\Input\Text\Position\Move
{
    /**
     * Preprocess value before save: return 1 or 0
     *
     * @param mixed $value Value
     *
     * @return array
     */
    protected function preprocessValueBeforeSave($value)
    {
        return [
            'position' => $value,
            'category' => $this->getCategoryId(),
        ];
    }

    /**
     * Get entity value
     *
     * @return mixed
     */
    protected function getEntityValue()
    {
        return $this->getEntity()->getPosition();
    }
}