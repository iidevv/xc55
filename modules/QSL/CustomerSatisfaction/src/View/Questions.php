<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CustomerSatisfaction\View;

class Questions extends \XLite\View\AView
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), ['questions']);
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/CustomerSatisfaction/questions/body.twig';
    }

    /**
     * Check - search box is visible or not
     *
     * @return bool
     */
    protected function isSearchVisible()
    {
        return 0 < \XLite\Core\Database::getRepo('QSL\CustomerSatisfaction\Model\Question')->count();
    }
}
