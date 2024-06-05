<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActProMembership\View;

class ProMembershipIcon extends \XLite\View\AView
{
    protected function isVisible()
    {
        if (!$this->product) {
            return false;
        }

        return parent::isVisible()
            && $this->product->hasFreeShippingIcon()
            && $this->isProMembershipIconVisible();
    }

    protected function getCacheParameters()
    {
        $list = parent::getCacheParameters();
        $list[] = $this->product ? $this->product->getProductId() : 0;
        $list[] = $this->product ? $this->product->getEntityVersion() : '';

        return $list;
    }

    protected function isCacheAvailable()
    {
        return true;
    }

    protected function getDefaultTemplate()
    {
        return 'modules/Qualiteam/SkinActProMembership/pro_membership_icon.twig';
    }

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/Qualiteam/SkinActProMembership/css/pro_membership_icon.css';
        return $list;
    }

    protected function getOnClick()
    {
        static $result = null;

        if ($result === null) {
            $pid = (int)\XLite\Core\Config::getInstance()->Qualiteam->SkinActProMembership->product_to_add;

            if ($pid > 0) {
                $link = $this->buildURL('product', '', ['product_id' => $pid]);
                $result = 'onclick="window.location.href=\'' . $link . '\';"';
                return $result;
            }

            $result = '';
            return $result;
        }

        return $result;
    }

}