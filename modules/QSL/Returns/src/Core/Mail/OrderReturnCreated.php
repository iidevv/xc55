<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Returns\Core\Mail;

class OrderReturnCreated extends \XLite\Core\Mail\Order\ACustomer
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

    public function __construct(\XLite\Model\Order $order)
    {
        parent::__construct($order);

        if ($order && is_object($order)) {
            $return = $order->getOrderReturn();
        }

        $returnFound = ! empty($return)
            && is_object($return)
            && $return instanceof \QSL\Returns\Model\OrderReturn;

        $this->populateVariables([
            'order_return_reason' => $returnFound ? $order->getOrderReturnReason() : '',
            'order_return_action' => $returnFound ? $order->getOrderReturnAction() : '',
            'return_action_text' => $returnFound && \QSL\Returns\Main::isActionsEnabled()
                ? '<p>' . self::t('Action') . ": {$order->getOrderReturnAction()}</p>"
                : '',
            'order_return_comment' => $returnFound ? nl2br($return->getComment(), false) : '',
        ]);
    }
}
