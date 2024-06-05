<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductQuestions\View\Page;

use XCart\Extender\Mapping\ListChild;

/**
 * Questions page view
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class Questions extends \XLite\View\AView
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), ['product_questions']);
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/ProductQuestions/questions/body.twig';
    }

    /**
     * Check - search box is visible or not
     *
     * @return boolean
     */
    protected function isSearchVisible()
    {
        return false;
    }
}
