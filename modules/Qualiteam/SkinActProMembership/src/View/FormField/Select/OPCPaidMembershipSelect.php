<?php

/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActProMembership\View\FormField\Select;


use XLite\Core\Auth;
use XLite\Core\Database;

class OPCPaidMembershipSelect extends \XLite\View\FormField\Select\Regular
{

    public static function isVisibleStatic()
    {
        $profile = Auth::getInstance()->getProfile();
        $profileMembership = null;

        if ($profile && !$profile->getAnonymous()) {
            $profileMembership = Auth::getInstance()->getProfile()->getMembership();
        }

        $pid = (int)\XLite\Core\Config::getInstance()->Qualiteam->SkinActProMembership->product_to_add;
        $product = Database::getRepo('XLite\Model\Product')->find($pid);

        //$isVisible = $profile && !$profile->getAnonymous();
        $isVisible = true;

        if ($pid === 0 || !$product) {
            $isVisible = false;
        }

        if ($profileMembership
            && $product
            && $product->getAppointmentMembership()
            && $product->getAppointmentMembership() === $profileMembership
        ) {
            $isVisible = false;
        }

        return $isVisible;
    }

    public function getFieldId()
    {
        return 'OPCPaidMembershipSelect';
    }

    protected function getDefaultValue()
    {
        $pid = (int)\XLite\Core\Config::getInstance()->Qualiteam->SkinActProMembership->product_to_add;
        $product = Database::getRepo('XLite\Model\Product')->find($pid);
        if ($product) {
            foreach ($this->getCart()->getItems() as $item) {
                if ($item->getProduct() === $product) {
                    return '1';
                }
            }
        }

        return parent::getDefaultValue();
    }

    protected function getDefaultOptions()
    {
        return [
            '0' => static::t('SkinActProMembership select No'),
            '1' => static::t('SkinActProMembership select Yes'),
        ];
    }

    protected function getDefaultLabel()
    {
        return static::t('SkinActProMembership OPCPaidMembershipSelect label');
    }
}