<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

/** @noinspection PhpMissingParamTypeInspection */
/** @noinspection ReturnTypeCanBeDeclaredInspection */
/** @noinspection PhpMissingReturnTypeInspection */

namespace CDev\GoogleAnalytics\Model;

use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Profile extends \XLite\Model\Profile
{
    /**
     * Google Analytics cid
     *
     * @var string
     * @ORM\Column (type="string")
     */
    protected $gaClientId = '';

    /**
     * Google Analytics session id
     *
     * @var string
     * @ORM\Column (type="string", nullable=true)
     */
    protected $gaSessionId = '';

    /**
     * @return string
     */
    public function getGaClientId()
    {
        return $this->gaClientId;
    }

    /**
     * @param string $gaClientId
     */
    public function setGaClientId($gaClientId)
    {
        $this->gaClientId = $gaClientId;
    }

    public function getGaSessionId()
    {
        return $this->gaSessionId;
    }

    public function setGaSessionId($gaSessionId)
    {
        $this->gaSessionId = $gaSessionId;
        return $this;
    }
}
