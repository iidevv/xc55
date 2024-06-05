<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Returns\Controller\Admin;

class ReturnReasons extends \XLite\Controller\Admin\AAdmin
{
    /**
     * @return bool
     */
    public function checkACL()
    {
        return parent::checkACL() || \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage orders');
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return static::t('Settings');
    }

    protected function doActionUpdate()
    {
        \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption([
            'category' => 'QSL\Returns',
            'name'     => 'hide_other_reason',
            'value'    => \XLite\Core\Request::getInstance()->hide_other_reason,
        ]);

        $list = new \QSL\Returns\View\ItemsList\Model\ReturnReason();

        $list->processQuick();
    }
}
