<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActMobileAppBanners\Model;


use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Banner extends \QSL\Banner\Model\Banner
{

    /**
     * @var   boolean
     *
     * @ORM\Column (type="boolean", options={ "default": false }, nullable=true)
     */
    protected $forMobileOnly = false;

    /**
     * @return bool
     */
    public function getForMobileOnly()
    {
        return $this->forMobileOnly;
    }

    /**
     * @param bool $forMobileOnly
     */
    public function setForMobileOnly($forMobileOnly)
    {
        $this->forMobileOnly = $forMobileOnly;
    }



}