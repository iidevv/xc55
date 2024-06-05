<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GDPR\Model;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Extender\Mixin
 */
class Profile extends \XLite\Model\Profile
{
    /**
     * Is user GDPR consent
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $gdprConsent = false;

    /**
     * Is user all cookies consent
     *
     * @var boolean
     *
     * @ORM\Column (type="string", length=32, nullable=true)
     */
    protected $allCookiesConsent = null;

    /**
     * Is user default cookies consent
     *
     * @var boolean
     *
     * @ORM\Column (type="string", length=32, nullable=true)
     */
    protected $defaultCookiesConsent = null;

    /**
     * Return GdprConsent
     *
     * @return bool
     */
    public function isGdprConsent()
    {
        return $this->gdprConsent;
    }

    /**
     * Set GdprConsent
     *
     * @param bool $gdprConsent
     *
     * @return $this
     */
    public function setGdprConsent($gdprConsent)
    {
        $this->gdprConsent = $gdprConsent;
        return $this;
    }

    /**
     * Return allCookiesConsent
     *
     * @return bool
     */
    public function isAllCookiesConsent()
    {
        return $this->allCookiesConsent === \XLite\Core\Auth::getCookieHash();
    }

    /**
     * Set allCookiesConsent
     *
     * @param bool $allCookiesConsent
     *
     * @return $this
     */
    public function setAllCookiesConsent($allCookiesConsent)
    {
        $this->setDefaultCookiesConsent(!$allCookiesConsent);
        $this->allCookiesConsent = $allCookiesConsent ? \XLite\Core\Auth::getCookieHash() : null;

        return $this;
    }

    public function getAllCookiesConsent(): ?string
    {
        return $this->allCookiesConsent;
    }

    /**
     * Return defaultCookiesConsent
     *
     * @return bool
     */
    public function isDefaultCookiesConsent()
    {
        return $this->defaultCookiesConsent === \XLite\Core\Auth::getCookieHash();
    }

    /**
     * Set defaultCookiesConsent
     *
     * @param bool $defaultCookiesConsent
     *
     * @return $this
     */
    public function setDefaultCookiesConsent($defaultCookiesConsent)
    {
        $this->defaultCookiesConsent = $defaultCookiesConsent ? \XLite\Core\Auth::getCookieHash() : null;

        return $this;
    }

    public function getDefaultCookiesConsent(): ?string
    {
        return $this->defaultCookiesConsent;
    }
}
