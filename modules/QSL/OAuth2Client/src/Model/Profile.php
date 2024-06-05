<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OAuth2Client\Model;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;

/**
 * Profile
 * @Extender\Mixin
 */
class Profile extends \XLite\Model\Profile
{
    /**
     * External profiles
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="QSL\OAuth2Client\Model\ExternalProfile", mappedBy="profile", cascade={"all"})
     */
    protected $external_profiles;

    /**
     * @inheritdoc
     */
    public function __construct(array $data = [])
    {
        $this->external_profiles = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Get external profile by provider
     *
     * @param \QSL\OAuth2Client\Model\Provider $provider Provider
     *
     * @return \QSL\OAuth2Client\Model\ExternalProfile
     */
    public function getExternalProfileByProvider(\QSL\OAuth2Client\Model\Provider $provider)
    {
        $result = null;

        /** @var \QSL\OAuth2Client\Model\ExternalProfile $profile */ #nolint
        foreach ($this->getExternalProfiles() as $profile) {
            if ($profile->getProvider()->getServiceName() == $provider->getServiceName()) {
                $result = $profile;
                break;
            }
        }

        return $result;
    }

    // {{{ Getters / setters

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getExternalProfiles()
    {
        return $this->external_profiles;
    }

    /**
     * @param \QSL\OAuth2Client\Model\ExternalProfile $external_profile
     *
     * @return static
     */
    public function addExternalProfiles(\QSL\OAuth2Client\Model\ExternalProfile $external_profile)
    {
        $this->external_profiles[] = $external_profile;

        return $this;
    }

    // }}}
}
