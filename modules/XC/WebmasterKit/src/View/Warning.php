<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\WebmasterKit\View;

use Includes\Utils\Module\Manager;
use XCart\Extender\Mapping\ListChild;

/**
 * Warning
 *
 * @ListChild (list="dashboard-center", zone="admin", weight="10")
 */
class Warning extends \XLite\View\Dialog
{
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = 'modules/XC/WebmasterKit/warning/warning.css';

        return $list;
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/XC/WebmasterKit/warning';
    }

    /**
     * Returns warning message
     *
     * @return string
     */
    protected function getMessage()
    {
        return static::t(
            'If the store is being run in production, it is strongly recommended NOT to keep the module Webmaster Kit enabled',
            ['url' => $this->getURL()]
        );
    }

    /**
     * Returns webmaster kit module url
     *
     * @return string
     */
    protected function getURL()
    {
        return Manager::getRegistry()->getModuleServiceURL('XC', 'WebmasterKit');
    }

    /**
     * Change visible condition, so visible only for root admin
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && !LC_DEVELOPER_MODE
            && \XLite\Core\Auth::getInstance()->isPermissionAllowed(\XLite\Model\Role\Permission::ROOT_ACCESS);
    }
}
