<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace Qualiteam\SkinActVerifiedCustomer\Model;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table  (name="verification_info")
 */
class VerificationInfo extends \XLite\Model\AEntity
{
    /**
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column (type="integer", options={ "unsigned": true })
     *
     * @var int
     */
    protected $id;

    const STATUS_VERIFIED = 'V';
    const STATUS_NOT_VERIFIED = 'N';

    /**
     * @ORM\Column (type="string", options={ "fixed": true }, length=1)
     *
     * @var string
     */
    protected $status = self::STATUS_NOT_VERIFIED;

    /**
     * @var \XLite\Model\Profile
     *
     * @ORM\OneToOne (targetEntity="XLite\Model\Profile", mappedBy="verificationInfo")
     *
     */
    protected $profile;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="Qualiteam\SkinActVerifiedCustomer\Model\VerificationFile", mappedBy="verificationInfo")
     * @ORM\OrderBy   ({"id" = "ASC"})
     */
    protected $files;


    public function addFiles($file)
    {
        $this->files[] = $file;

        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFiles()
    {
        return $this->files;
    }

    public function __construct(array $data = [])
    {
        $this->files = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * @return \XLite\Model\Profile
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * @param \XLite\Model\Profile $profile
     */
    public function setProfile($profile)
    {
        $this->profile = $profile;
    }


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }


}