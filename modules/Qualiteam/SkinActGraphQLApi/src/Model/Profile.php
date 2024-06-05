<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Model;

use Doctrine\ORM\Mapping as ORM;



use Doctrine\Common\Collections\Collection;

/**
 * Class represents an order
 */
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin 
 * 

 */

abstract class Profile extends \XLite\Model\Profile
{
    const USER_TYPE_CUSTOMER = 'C';
    const USER_TYPE_STAFF    = 'A';

    /**
     * Devices
     *
     * @var Collection|Device[]
     *
     * @ORM\OneToMany (targetEntity="\Qualiteam\SkinActGraphQLApi\Model\Device", mappedBy="profile", cascade={"remove"})
     */
    protected $devices;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     *
     * @return void
     */
    public function __construct(array $data = [])
    {
        $this->devices = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * @return Collection|Device[]
     */
    public function getDevices()
    {
        return $this->devices ?: [];
    }

    /**
     * @param Collection|Device[] $devices
     *
     * @return static
     */
    public function setDevices($devices)
    {
        $this->devices = $devices;

        return $this;
    }

    /**
     * @param Device $device
     *
     * @return static
     */
    public function addDevice($device)
    {
        $this->devices[] = $device;

        return $this;
    }
}
