<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\Model;

use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Profile extends \XLite\Model\Profile
{
    /**
     * Auth provider (paypal)
     *
     * @var string
     *
     * @ORM\Column (type="string", length=128, nullable=true)
     */
    protected $socialLoginProvider;

    /**
     * Auth provider-unique user id (for ex. facebook user id)
     *
     * @var string
     *
     * @ORM\Column (type="string", length=128, nullable=true)
     */
    protected $socialLoginId;

    /**
     * Checks if current profile is a SocialLogin's profile
     *
     * @return boolean
     */
    public function isSocialProfile()
    {
        return (bool) $this->getSocialLoginProvider();
    }

    /**
     * Set socialLoginProvider
     *
     * @param string $socialLoginProvider
     * @return Profile
     */
    public function setSocialLoginProvider($socialLoginProvider)
    {
        $this->socialLoginProvider = $socialLoginProvider;
        return $this;
    }

    /**
     * Get socialLoginProvider
     *
     * @return string
     */
    public function getSocialLoginProvider()
    {
        return $this->socialLoginProvider;
    }

    /**
     * Set socialLoginId
     *
     * @param string $socialLoginId
     * @return Profile
     */
    public function setSocialLoginId($socialLoginId)
    {
        $this->socialLoginId = $socialLoginId;
        return $this;
    }

    /**
     * Get socialLoginId
     *
     * @return string
     */
    public function getSocialLoginId()
    {
        return $this->socialLoginId;
    }
}
