<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCategoryDescriptionAndFaq\View;

use XCart\Extender\Mapping\ListChild;

/**
 * Category bottom description widget
 *
 * @ListChild (list="category-bottom.element", zone="customer", weight="100")
 */
class CategoryBottomDescription extends \XLite\View\AView
{
    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();
        $result[] = 'category';

        return $result;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'layout/content/category_bottom_description.twig';
    }

    /**
     * Return bottom description with postprocessing WEB LC root constant
     *
     * @return string
     */
    protected function getBottomDescription()
    {
        return $this->getCategory()->getViewBottomDescription();
    }
}
