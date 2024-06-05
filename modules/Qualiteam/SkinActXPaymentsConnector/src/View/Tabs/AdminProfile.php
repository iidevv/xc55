<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsConnector\View\Tabs;

use Qualiteam\SkinActXPaymentsConnector\Model\Payment\Processor\SavedCard;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Auth;
use XLite\Core\CommonCell;
use XLite\Core\Database;
use XLite\Model\Payment\Method;

/**
 * Profile dialog
 *
 * @Extender\Mixin
 */
class AdminProfile extends \XLite\View\Tabs\AdminProfile
{
    /**
     * Returns the list of targets where this widget is available
     *
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'saved_cards';

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

        $profile = (null !== $this->getProfile())
            ? $this->getProfile()
            : Auth::getInstance()->getProfile();
        $isAnonymous = $profile->getAnonymous();

        if ($saveCardsMethods && !$isAnonymous) {
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
