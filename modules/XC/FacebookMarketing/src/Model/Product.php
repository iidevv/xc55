<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FacebookMarketing\Model;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;

/**
 * The "product" model class
 * @Extender\Mixin
 */
class Product extends \XLite\Model\Product
{
    /**
     * Product is available for Facebook Marketing feed
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean", options={"default" : true})
     */
    protected $facebookMarketingEnabled = true;

    /**
     * Return FacebookMarketingEnabled
     *
     * @return boolean
     */
    public function getFacebookMarketingEnabled()
    {
        return $this->facebookMarketingEnabled;
    }

    /**
     * Set FacebookMarketingEnabled
     *
     * @param boolean $facebookMarketingEnabled
     *
     * @return $this
     */
    public function setFacebookMarketingEnabled($facebookMarketingEnabled)
    {
        $this->facebookMarketingEnabled = $facebookMarketingEnabled;
        return $this;
    }

    /**
     * Return product identifier for facebook pixel
     *
     * @return string
     */
    public function getFacebookPixelProductIdentifier()
    {
        return $this->getSku();
    }
}
