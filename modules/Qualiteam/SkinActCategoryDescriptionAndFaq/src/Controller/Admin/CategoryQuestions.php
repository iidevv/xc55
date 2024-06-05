<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCategoryDescriptionAndFaq\Controller\Admin;

class CategoryQuestions extends \XLite\Controller\Admin\AAdmin
{
    /**
     * 'selectorData' target used to get top links for selector on edit product page
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        return array_unique(array_merge(parent::defineFreeFormIdActions(), ['selectorData']));
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return static::t('SkinActCategoryDescriptionAndFaq Category Questions');
    }
}
