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
class BannerSlide extends \QSL\Banner\Model\BannerSlide
{
    const APP_POSITION_1 = 1;
    const APP_POSITION_2 = 2;
    const APP_POSITION_3 = 3;

    /**
     * @var integer
     *
     * @ORM\Column (type="integer", nullable=true)
     */
    protected $appPosition;

    /**
     * @return int
     */
    public function getAppPosition()
    {
        return $this->appPosition;
    }

    /**
     * @param int $appPosition
     */
    public function setAppPosition($appPosition)
    {
        $this->appPosition = $appPosition;
    }

}