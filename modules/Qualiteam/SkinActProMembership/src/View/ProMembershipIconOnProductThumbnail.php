<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActProMembership\View;

use XCart\Extender\Mapping\ListChild;


/**
 * @ListChild(list="product.details.page.image.photo", weight="9999", zone="customer")
 */
class ProMembershipIconOnProductThumbnail extends \XLite\View\AView
{
    public function __construct(array $params = [])
    {
        parent::__construct($params);
        $this->product = $this->getProduct();
    }

    protected function isVisible()
    {
        if (!$this->product) {
            return false;
        }

        return parent::isVisible()
            && $this->product->hasFreeShippingIcon();
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
        return 'modules/Qualiteam/SkinActProMembership/pro_membership_icon_product_page.twig';
    }

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/Qualiteam/SkinActProMembership/css/pro_membership_icon_product_page.css';
        return $list;
    }

    protected function getOnClick()
    {
        return '';
    }

}