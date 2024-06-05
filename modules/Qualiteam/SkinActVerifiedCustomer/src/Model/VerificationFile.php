<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVerifiedCustomer\Model;


use Doctrine\ORM\Mapping as ORM;
use XLite\Core\Request;

/**
 * Content images file storage
 *
 * @ORM\Entity
 * @ORM\Table  (name="verification_files")
 */
class VerificationFile extends \XLite\Model\Base\Storage
{

    /**
     * @var \Qualiteam\SkinActVerifiedCustomer\Model\VerificationInfo
     *
     * @ORM\ManyToOne  (targetEntity="Qualiteam\SkinActVerifiedCustomer\Model\VerificationInfo", inversedBy="files")
     * @ORM\JoinColumn (name="verification_info_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $verificationInfo;

    /**
     * @return \Qualiteam\SkinActVerifiedCustomer\Model\VerificationInfo
     */
    public function getVerificationInfo()
    {
        return $this->verificationInfo;
    }

    /**
     * @param \Qualiteam\SkinActVerifiedCustomer\Model\VerificationInfo $verificationInfo
     */
    public function setVerificationInfo($verificationInfo)
    {
        $this->verificationInfo = $verificationInfo;
    }

    public function isExists()
    {
        return true;
    }

    public function getExtensionByMIME()
    {
        return pathinfo($this->getFileName(), PATHINFO_EXTENSION);
    }
}


