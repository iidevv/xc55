<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActProMembership\Helpers;

use XLite\Core\Auth;

class Profile
{
    protected $profile;

    public function __construct(\XLite\Model\Profile $profile = null)
    {
        $this->profile = $profile;
    }

    public function isProfileProMembership()
    {
        return Auth::getInstance()->isLogged()
            && $this->isProfileMembership()
            && $this->hasProMembershipProduct()
            && $this->isProfileMembershipEqualProMemberhsip();
    }

    protected function isProfileMembership()
    {
        return (bool) $this->getProfileMembership();
    }

    protected function getProfileMembership()
    {
        return $this->getProfile()->getMembership();
    }

    protected function getProfileMembershipId()
    {
        return $this->getProfileMembership()->getMembershipId();
    }

    protected function getProfile()
    {
        return $this->profile ?? Auth::getInstance()->getProfile();
    }

    protected function hasProMembershipProduct()
    {
        return (bool) $this->getProMembershipProduct();
    }

    protected function getProMembershipProduct()
    {
        return ProMembershipProducts::getProMembershipProduct();
    }

    protected function getAppointmentMembership()
    {
        return $this->getProMembershipProduct()->getAppointmentMembership();
    }

    protected function getProMembershipProductId()
    {
        return $this->getAppointmentMembership()->getMembershipId();
    }

    protected function isProfileMembershipEqualProMemberhsip()
    {
        return $this->getProfileMembershipId() === $this->getProMembershipProductId();
    }
}