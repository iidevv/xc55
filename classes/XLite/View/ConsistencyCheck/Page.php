<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ConsistencyCheck;

use XCart\Extender\Mapping\ListChild;

/**
 * Class Page
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class Page extends \XLite\View\AView
{
    /**
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(
            parent::getAllowedTargets(),
            [
                'consistency_check'
            ]
        );
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'consistency_check/page.twig';
    }

    /**
     * @return boolean
     */
    public function hasResults()
    {
        return method_exists(\XLite::getController(), 'hasInconsistencies')
            ? \XLite::getController()->hasInconsistencies()
            : false;
    }

    /**
     * @return array
     */
    public function getGroups()
    {
        return method_exists(\XLite::getController(), 'getInconsistencies')
            ? \XLite::getController()->getInconsistencies()
            : [];
    }
}
