<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
 
namespace QSL\BraintreeVZ\View\Tabs;

use XCart\Extender\Mapping\Extender;

/**
 * Profile dialog
 * @Extender\Mixin
 */
class Account extends \XLite\View\Tabs\Account
{
    /**
     * Returns the list of targets where this widget is available
     *
     * @return void
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'braintree_credit_cards';

        return $list;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {

        $tabs = parent::defineTabs();

        if (
            $this->getProfile()
            && \QSL\BraintreeVZ\Core\BraintreeClient::getInstance()->isDisplayCardsTab()
        ) {
            $tabs['braintree_credit_cards'] = array(
                 'weight'   => 1100,
                 'title'    => static::t('Braintree credit cards'),
                 'template' => 'modules/QSL/BraintreeVZ/account/braintree_credit_cards_tab.twig',
            );
        }

        return $tabs;
    }
}

