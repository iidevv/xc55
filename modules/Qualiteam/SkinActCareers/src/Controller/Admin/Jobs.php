<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCareers\Controller\Admin;


use XLite\Core\Database;

class Jobs extends \XLite\Controller\Admin\AAdmin
{
    public function getTitle()
    {
        return static::t('SkinActCareers Careers');
    }

    protected function doActionDelete()
    {
        $select = \XLite\Core\Request::getInstance()->select;

        if ($select === null) {
            Database::getRepo('\Qualiteam\SkinActCareers\Model\Job')->clearAll();
            \XLite\Core\TopMessage::addInfo('SkinActCareers All entries have been deleted');
        } else {
            parent::doActionDelete();
        }

    }
}