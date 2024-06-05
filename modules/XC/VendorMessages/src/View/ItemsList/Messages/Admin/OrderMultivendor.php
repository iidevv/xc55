<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\VendorMessages\View\ItemsList\Messages\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * All admin messages
 *
 * @Extender\Mixin
 * @Extender\After ("XC\VendorMessages")
 * @Extender\Depend ("XC\MultiVendor")
 */
class OrderMultivendor extends \XC\VendorMessages\View\ItemsList\Messages\Admin\Order
{
    /**
     * @inheritdoc
     */
    protected function isEmailVisible(\XC\VendorMessages\Model\Message $message)
    {
        $result = parent::isEmailVisible($message);
        $auth = \XLite\Core\Auth::getInstance();
        $config = \XLite\Core\Config::getInstance()->XC->MultiVendor;

        return \XLite::isAdminZone()
            && (
                ($auth->isVendor() && $result && !$config->mask_contacts)
                || (!$auth->isVendor() && $result)
                || (!$auth->isVendor() && $message->getAuthor()->isVendor())
            );
    }

    /**
     * @param \XC\VendorMessages\Model\Message $message
     *
     * @return mixed
     */
    protected function getEmail(\XC\VendorMessages\Model\Message $message)
    {
        $email = parent::getEmail($message);

        return $message->getAuthor()->isVendor()
            ? $message->getAuthor()->getLogin()
            : $email;
    }

    /**
     * @inheritdoc
     */
    protected function getWidgetParameters()
    {
        return parent::getWidgetParameters() + [
            'recipient_id' => \XLite\Core\Request::getInstance()->recipient_id,
        ];
    }

    /**
     * @inheritdoc
     */
    protected function getCommonParams()
    {
        $initialize = !isset($this->commonParams);

        $this->commonParams = parent::getCommonParams();

        if ($initialize) {
            $this->commonParams += [
                'recipient_id' => \XLite\Core\Request::getInstance()->recipient_id,
            ];
        }

        return $this->commonParams;
    }
}
