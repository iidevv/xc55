<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsConnector\View\StickyPanel;

use XLite\View\Button\AButton;
use XLite\View\Button\Link;
use XLite\View\StickyPanel\ItemsListForm;

/**
 * Payment methods list buttons (sticky panel) 
 */
class PaymentMethods extends ItemsListForm
{
    /**
     * Sticky panel must be visible always so "Re-import" is available
     *
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass() . ' always-visible';
    }

    /**
     * Check panel has more actions buttons
     *
     * @return boolean
     */
    protected function hasMoreActionsButtons()
    {
        return true;
    }

    /**
     * Define buttons widgets
     *
     * @return array
     */
    protected function defineButtons()
    {
        $list = parent::defineButtons();

        $list['import'] = $this->getWidget(
            array(
                AButton::PARAM_STYLE    => 'always-enabled',
                AButton::PARAM_LABEL    => 'Re-import payment methods',
                AButton::PARAM_DISABLED => false
            ),
            \Qualiteam\SkinActXPaymentsConnector\View\Button\PaymentMethods\Import::class
        );

        $list['add_new'] = $this->getWidget(
            array(
                AButton::PARAM_STYLE     => 'action link always-enabled',
                AButton::PARAM_LABEL     => 'Add new payment method',
                AButton::PARAM_DISABLED  => false,
                Link::PARAM_BLANK        => true,
            ),
            \Qualiteam\SkinActXPaymentsConnector\View\Button\PaymentMethods\AddNew::class
        );

        return $list;
    }
}
