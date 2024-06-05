<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsConnector\View;

use XCart\Extender\Mapping\ListChild;
use XLite\Core\Database;
use XLite\Core\Request;
use XLite\Model\Order;
use XLite\View\AView;

/**
 * Popup payment additional info 
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class PopupSavedCards extends AView
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), array('popup_saved_cards'));
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/style.css';

        return $list;
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/Qualiteam/SkinActXPaymentsConnector/order/saved_cards';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/body.twig';
    }

    /**
     * Return recharge amount 
     *
     * @return string
     */
    protected function getAmount()
    {
        return $this->formatPrice(Request::getInstance()->amount);
    }

    /**
     * Return customer's saved credit cards 
     *
     * @return array 
     */
    protected function getCards()
    {
        $order = Database::getRepo(Order::class)->findOneByOrderNumber(
            Request::getInstance()->order_number
        );

        $cards = false;

        if (
            $order
            && $order->getOrigProfile()
        ) {
            $cards = $order->getOrigProfile()->getSavedCards();
        }

        return $cards;
    }

    /**
     * Get formatted card name 
     *
     * @return array
     */
    protected function getCardName($card)
    {
        $type = $card['card_type'] . str_repeat('&nbsp;', 4 - strlen($card['card_type']));

        return $type . ' ' . $card['card_number'];
    }
}
