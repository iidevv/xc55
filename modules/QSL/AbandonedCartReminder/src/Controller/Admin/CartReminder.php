<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\Controller\Admin;

/**
 * Controller for the Edit Reminder page.
 */
class CartReminder extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Controller parameters.
     *
     * @var array
     */
    protected $params = ['target', 'id'];

    /**
     * Check ACL permissions.
     *
     * @return boolean
     */
    public function checkACL()
    {
        return parent::checkACL() || \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage orders');
    }

    /**
     * Return the current page title (for the content area).
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Automated messages');
    }

    /**
     * Return the title as the page subtitle.
     *
     * @return string
     */
    public function getSubtitle()
    {
        return '';
    }

    /**
     * Update model.
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        if ($this->getModelForm()->performAction('modify')) {
            $this->setReturnUrl(\XLite\Core\Converter::buildURL('cart_reminders'));
        }
    }

    /**
     * Get model form class.
     *
     * @return string
     */
    protected function getModelFormClass()
    {
        return 'QSL\AbandonedCartReminder\View\Model\Reminder';
    }
}
