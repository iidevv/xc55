<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Concierge\Model;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;

/**
 * Module
 * @Extender\Mixin
 */
abstract class Profile extends \XLite\Model\Profile
{
    /**
     * @var string
     *
     * @ORM\Column (type="string", length=128, nullable=true)
     */
    protected $conciergeUserId;

    /**
     * @return string
     */
    public function getConciergeUserId()
    {
        return $this->conciergeUserId;
    }

    /**
     * @param string $conciergeUserId
     */
    public function setConciergeUserId($conciergeUserId)
    {
        $this->conciergeUserId = $conciergeUserId;
    }
}
