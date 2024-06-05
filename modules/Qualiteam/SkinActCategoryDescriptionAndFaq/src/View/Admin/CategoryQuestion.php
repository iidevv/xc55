<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCategoryDescriptionAndFaq\View\Admin;

use XCart\Extender\Mapping\ListChild;

/**
 * Category Question modify page view
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class CategoryQuestion extends \XLite\View\AView
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), ['category_question']);
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/Qualiteam/SkinActCategoryDescriptionAndFaq/category_question.twig';
    }
}
