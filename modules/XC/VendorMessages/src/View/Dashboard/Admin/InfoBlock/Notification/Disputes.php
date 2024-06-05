<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\VendorMessages\View\Dashboard\Admin\InfoBlock\Notification;

use XCart\Extender\Mapping\Extender;
use XCart\Extender\Mapping\ListChild;
use XLite\Core\Auth;
use XLite\Core\Cache\ExecuteCachedTrait;
use XLite\Core\Database;
use XC\VendorMessages\Main as VendorMessagesMain;
use XC\VendorMessages\Model\Message;

/**
 * @ListChild (list="dashboard.info_block.notifications", weight="200", zone="admin")
 * @Extender\Depend ("XC\MultiVendor")
 */
class Disputes extends \XLite\View\Dashboard\Admin\InfoBlock\ANotification
{
    use ExecuteCachedTrait;

    /**
     * @return string
     */
    protected function getNotificationType()
    {
        return 'XCVendorMessagesDisputes';
    }

    /**
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass() . ' xc-vendormessages-disputes';
    }

    /**
     * @return string
     */
    protected function getHeader()
    {
        return static::t('Disputes');
    }

    /**
     * @return string
     */
    protected function getHeaderUrl()
    {
        return $this->buildURL(
            'messages',
            '',
            [
                'messages' => 'D',
            ]
        );
    }

    /**
     * Get entries count
     *
     * @return integer
     */
    protected function getCounter()
    {
        return $this->executeCachedRuntime(static function () {
            return Database::getRepo(Message::class)->countDisputes();
        });
    }

    /**
     * @return bool
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && VendorMessagesMain::isAllowDisputes()
            && $this->getCounter();
    }

    /**
     * @return bool
     */
    protected function checkACL()
    {
        return parent::checkACL()
            && (Auth::getInstance()->hasRootAccess()
                || Auth::getInstance()->isPermissionAllowed('manage conversations'));
    }
}
