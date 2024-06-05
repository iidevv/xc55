<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
 
 namespace Qualiteam\SkinActXPaymentsConnector\View\Tabs;

use Qualiteam\SkinActXPaymentsConnector\Model\Payment\Processor\SavedCard;
use XCart\Extender\Mapping\Extender;
use XLite\Core\CommonCell;
use XLite\Core\Database;
use XLite\Model\Payment\Method;

/**
 * Profile dialog
 *
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
        $list[] = 'saved_cards';
        $list[] = 'add_new_card';

        return $list;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        $tabs = parent::defineTabs();

        $cnd = new CommonCell();
        $cnd->class = SavedCard::class;

        $saveCardsMethods = Database::getRepo(Method::class)->search($cnd);

        if ($saveCardsMethods) {
            $found = false;
            foreach ($saveCardsMethods as $pm) {
                if ($pm->isEnabled()) {
                    $found = true;
                    break;
                }
            }

            if ($found) {
                $tabs['saved_cards'] = array(
                    'weight'   => 1000,
                    'title'    => static::t('Saved credit cards'),
                    'template' => 'modules/Qualiteam/SkinActXPaymentsConnector/account/saved_cards.twig',
                );
            }
        }

        return $tabs;
    }
}
