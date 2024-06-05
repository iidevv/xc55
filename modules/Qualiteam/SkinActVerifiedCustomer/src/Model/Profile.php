<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace Qualiteam\SkinActVerifiedCustomer\Model;

use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Database;


/**
 * @Extender\Mixin
 */
class Profile extends \XLite\Model\Profile
{

    /**
     * @var VerificationInfo
     *
     * @ORM\OneToOne (targetEntity="Qualiteam\SkinActVerifiedCustomer\Model\VerificationInfo", inversedBy="profile", cascade={"all"})
     * @ORM\JoinColumn (name="verification_info_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $verificationInfo;

    /**
     * @return VerificationInfo
     */
    public function getVerificationInfo()
    {
        return $this->verificationInfo;
    }

    /**
     * @param VerificationInfo $verificationInfo
     */
    public function setVerificationInfo($verificationInfo)
    {
        $this->verificationInfo = $verificationInfo;
    }


    public function isVerified()
    {
        if ($this->getVerificationInfo()) {
            return $this->getVerificationInfo()->getStatus() === VerificationInfo::STATUS_VERIFIED;
        }

        return false;
    }

    public function makeVerified()
    {
        if ($this->getVerificationInfo()) {

            $this->getVerificationInfo()->setStatus(VerificationInfo::STATUS_VERIFIED);

        } else {
            $verificationInfo = new \Qualiteam\SkinActVerifiedCustomer\Model\VerificationInfo();
            Database::getEM()->persist($verificationInfo);
            $verificationInfo->setStatus(VerificationInfo::STATUS_VERIFIED);
            $verificationInfo->setProfile($this);
            $this->setVerificationInfo($verificationInfo);
        }
    }

}