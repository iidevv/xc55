<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActProMembership\View;

use XCart\Extender\Mapping\ListChild;
use XLite\Core\Auth;
use XLite\Core\Database;

/**
 * @ListChild (list="center.top", zone="customer", weight="9999")
 */
class ForceCustomerRegister extends \XLite\View\AView
{

    public static function getAllowedTargets()
    {
        return ['checkout'];
    }

    protected function getDefaultTemplate()
    {
        return 'modules/Qualiteam/SkinActProMembership/force_customer_register.twig';
    }

    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/Qualiteam/SkinActProMembership/js/force_customer_register.js';
        return $list;
    }

    protected function isVisible()
    {
//        if (Auth::getInstance()->isAnonymous()) {
//
//            $pid = (int)\XLite\Core\Config::getInstance()->Qualiteam->SkinActProMembership->product_to_add;
//            $product = Database::getRepo('XLite\Model\Product')->find($pid);
//
//            $paidMembershipInCart = false;
//
//            foreach ($this->getCart()->getItems() as $item) {
//                if ($item->getProduct() === $product) {
//                    $paidMembershipInCart = true;
//                    break;
//                }
//            }
//
//            return $paidMembershipInCart;
//        }

        return true;
    }

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/Qualiteam/SkinActProMembership/css/password-fix.css';
        return $list;
    }

}