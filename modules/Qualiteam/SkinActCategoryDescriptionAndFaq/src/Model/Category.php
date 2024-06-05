<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCategoryDescriptionAndFaq\Model;

use XCart\Extender\Mapping\Extender;

/**
 * Category model
 * @Extender\Mixin
 */
class Category extends \XLite\Model\Category
{
    /**
     * Return category bottom description
     *
     * @return string
     */
    public function getViewBottomDescription()
    {
        return static::getPreprocessedValue($this->getBottomDescription())
            ?: $this->getBottomDescription();
    }

    // {{{ Translation Getters / setters
    /**
     * @return string
     */
    public function getBottomDescription()
    {
        return $this->getTranslationField(__FUNCTION__);
    }

    /**
     * @param string $bottomDescription
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setBottomDescription($bottomDescription)
    {
        return $this->setTranslationField(__FUNCTION__, $bottomDescription);
    }
    // }}}
}
