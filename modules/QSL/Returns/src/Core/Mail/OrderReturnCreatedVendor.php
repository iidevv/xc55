<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Returns\Core\Mail;

class OrderReturnCreatedVendor extends \XLite\Core\Mail\Order\AAdmin
{
    public static function getDir()
    {
        return 'modules/QSL/Returns/return/created';
    }

    protected static function defineVariables()
    {
        return [
                'order_return_reason' => static::t('Return reason'),
                'order_return_action' => static::t('Return action'),
                'return_action_text' => static::t('Return action text'),
                'order_return_comment' => static::t('Return comment'),
            ] + parent::defineVariables();
    }

    /**
     * AdvancedContactUsMessage constructor.
     *
     * @param  \XLite\Model\Order $data
     */
    public function __construct(\XLite\Model\Order $order)
    {
        // {{{ copied from \XC\MultiVendor\Core\Mail\AAdminOrder
        parent::__construct($order);

        $mailOrder = $order && !$order->getOrderNumber() && $order->isChild()
            ? $order->getParent()
            : $order;

        $this->appendData([
            'order' => $mailOrder,
        ]);

        if ($vendor = $order->getVendor()) {
            $this->setTo($vendor->getLogin());
            if (\XLite\Core\Config::getInstance()->Email->reply_to_vendor !== 'customer') {
                $this->setReplyTo([$this->getFrom()]);
            }
            $this->tryToSetLanguageCode($vendor->getLanguage());
            $this->populateVariables(['recipient_name' => $vendor->getVendorCompanyName() ?: $vendor->getName()]);
            $this->appendData(['displayForVendor' => $vendor]);
        }
        // }}}

        if ($mailOrder && is_object($mailOrder)) {
            $return = $mailOrder->getOrderReturn();
        }

        $returnFound = ! empty($return)
            && is_object($return)
            && $return instanceof \QSL\Returns\Model\OrderReturn;

        $this->populateVariables([
            'order_number' => $mailOrder->getOrderNumber(),
            'order_return_reason' => $returnFound ? $mailOrder->getOrderReturnReason() : '',
            'order_return_action' => $returnFound ? $mailOrder->getOrderReturnAction() : '',
            'return_action_text' => $returnFound && \QSL\Returns\Main::isActionsEnabled()
                ? '<p>' . self::t('Action') . ": {$order->getOrderReturnAction()}</p>"
                : '',
            'order_return_comment' => $returnFound ? nl2br($return->getComment(), false) : '',
        ]);
    }
}
