<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCustomOrderStatuses\Model\Order\Status;

use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender as Extender;

/**
 * @Extender\Mixin
 */
class Payment extends \XLite\Model\Order\Status\Payment
{
    /**
     * @ORM\Column (type="string", length=1, options={"default" : "A"})
     */
    protected $mobile_tab;

    /**
     * @return string|null
     */
    public function getMobileTab(): ?string
    {
        return $this->mobile_tab;
    }

    /**
     * @param string $mobile_tab
     */
    public function setMobileTab(string $mobile_tab): void
    {
        $this->mobile_tab = $mobile_tab;
    }


}