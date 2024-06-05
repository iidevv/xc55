<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\reCAPTCHA\Model;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Extender\Mixin
 */
class Profile extends \XLite\Model\Profile
{
    /**
     * reCAPTCHA Activation key
     *
     * @var   string
     *
     * @ORM\Column (type="string", length=64, nullable=true)
     */
    protected $recaptchaActivationKey = '';

    /**
     * Get reCAPTCHA activation key
     *
     * @return string
     */
    public function getRecaptchaActivationKey()
    {
        return $this->recaptchaActivationKey;
    }

    /**
     * Set membership
     *
     * @param string $key Activation key (if omitted, it will be generated automatically)
     *
     * @return void
     */
    public function setRecaptchaActivationKey($key = '')
    {
        $this->recaptchaActivationKey = $key ?: $this->generateRecaptchaActivationKey();
    }

    /**
     * Get random value for download key
     *
     * @return string
     */
    public function generateRecaptchaActivationKey()
    {
        return substr(
            hash('sha512', \XLite\Core\Database::getRepo('XLite\Model\Profile')->generatePassword()),
            0,
            64
        );
    }
}
