<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

use XCart\Extender\Mapping\ListChild;

/**
 * @ListChild (list="admin.h1.before", zone="admin")
 */
class RootUpgradeAlert extends \XLite\View\AView
{
    /**
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && self::isRootNotPublic();
    }

    /**
     * @return boolean
     */
    public function checkACL()
    {
        return parent::checkACL() && \XLite\Core\Auth::getInstance()->hasRootAccess();
    }

    /**
     * @return boolean
     */
    public static function isRootNotPublic(): bool
    {
        return defined('XC_DIR_ROOT');
    }

    /**
     * Get CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getJSFiles();
        $list[] = 'upgrade_block/style.less';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'upgrade_block/root_alert.twig';
    }
}
