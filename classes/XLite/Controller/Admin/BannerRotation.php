<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

class BannerRotation extends \XLite\Controller\Admin\Settings
{
    /**
     * Check ACL permissions
     *
     * @return bool
     */
    public function checkACL()
    {
        return parent::checkACL() || \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage banners');
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Front page');
    }

    /**
     * Update model
     */
    public function doActionUpdate()
    {
        $list = new \XLite\View\ItemsList\BannerRotationImages();
        $list->processQuick();

        $this->getModelForm()->performAction('update');
    }

    /**
     * getModelFormClass
     *
     * @return string
     */
    protected function getModelFormClass()
    {
        return 'XLite\View\Model\Settings';
    }

    /**
     * Get options for current tab (category)
     *
     * @param bool $getAllOptions
     *
     * @return \XLite\Model\Config[]
     */
    public function getOptions($getAllOptions = false)
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Config')->findByCategoryAndVisible('BannerRotation');
    }
}
