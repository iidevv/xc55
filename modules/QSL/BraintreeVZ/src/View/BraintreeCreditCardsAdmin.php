<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BraintreeVZ\View;

use XCart\Extender\Mapping\ListChild;

/**
 * Braintree credit cards
 * @ListChild (list="admin.center", zone="admin")
 */
class BraintreeCreditCardsAdmin extends \XLite\View\Dialog
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), array('braintree_credit_cards'));
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
        return 'modules/QSL/BraintreeVZ/account';
    }

    /**
     * Return file name for body template
     *
     * @return string
     */
    protected function getBodyTemplate()
    {
        return 'braintree_credit_cards.twig';
    }

}
