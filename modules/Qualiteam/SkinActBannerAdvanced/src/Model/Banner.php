<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActBannerAdvanced\Model;

use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender as Extender;

/**
 * @Extender\Mixin
 */
class Banner extends \QSL\Banner\Model\Banner
{
    /**
     * Sort position
     *
     * @var   integer
     *
     * @ORM\Column (type="integer", nullable=true)
     */
    protected $mobile_position = 0;

    /**
     * @param $mobile_position
     *
     * @return $this
     */
    public function setMobilePosition($mobile_position)
    {
        $this->mobile_position = $mobile_position;
        return $this;
    }

    /**
     * Get mobile position
     *
     * @return boolean
     */
    public function getMobilePosition()
    {
        return $this->mobile_position;
    }
}