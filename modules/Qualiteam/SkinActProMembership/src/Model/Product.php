<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActProMembership\Model;


use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;
use XLite\Core\Database;

/**
 * The "product" model class
 * @Extender\Mixin
 */
class Product extends \XLite\Model\Product
{

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany (targetEntity="XLite\Model\Membership", inversedBy="freeShippingProducts")
     *
     * @ORM\JoinTable(
     *     name="free_shipping_memberships",
     *     joinColumns={
     *          @ORM\JoinColumn(name="product_id", referencedColumnName="product_id")
     *     },
     *     inverseJoinColumns={
     *          @ORM\JoinColumn(name="membership_id", referencedColumnName="membership_id")
     *     }
     * )
     *
     */
    protected $freeShippingForMemberships;


    /**
     * @var boolean
     *
     * @ORM\Column (type="boolean", options={"default" : true}, nullable=true)
     */
    protected $showFreeShippingStamp = true;

    /**
     * @return bool
     */
    public function getShowFreeShippingStamp()
    {
        return $this->showFreeShippingStamp;
    }

    /**
     * @param bool $showFreeShippingStamp
     */
    public function setShowFreeShippingStamp($showFreeShippingStamp)
    {
        $this->showFreeShippingStamp = (bool)$showFreeShippingStamp;
    }


    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getFreeShippingForMemberships()
    {
        return $this->freeShippingForMemberships;
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $freeShippingForMemberships
     */
    public function setFreeShippingForMemberships($freeShippingForMemberships)
    {
        $this->freeShippingForMemberships = $freeShippingForMemberships;
    }

    public function clearFreeShippingForMemberships()
    {
        foreach ($this->freeShippingForMemberships as $membership) {
            $this->freeShippingForMemberships->removeElement($membership);
        }
    }

    public function addFreeShippingForMemberships($membership)
    {
        if (!$this->freeShippingForMemberships->contains($membership)) {
            $this->freeShippingForMemberships[] = $membership;
        }
    }

    public function removeFreeShippingForMemberships($membership)
    {
        if ($this->freeShippingForMemberships->contains($membership)) {
            $this->freeShippingForMemberships->removeElement($membership);
        }
    }

    public function hasFreeShippingIcon()
    {
        return $this->getFreeShippingForMemberships()->count() > 0
            && $this->getShowFreeShippingStamp();
    }

    public function getPaidMembership()
    {
        return (bool)$this->getAppointmentMembership();
    }

    public function setPaidMembership($value)
    {
        if (!$value) {
            // reset to defaults
            $this->setAppointmentMembership(null);
            $this->setAssignedMembershipTTLType(static::MEMBERSHIP_TTL_TYPE_NONE);
            $this->setAssignedMembershipTTL(1);
        }
    }

    public function freeShippingForMemberships()
    {
        $result = [];

        if ($this->freeShippingForMemberships) {

            foreach ($this->freeShippingForMemberships as $membership) {

                $result[] = $membership->getMembershipId();
            }
        }

        return $result;
    }

    public function __construct(array $data = [])
    {
        $this->freeShippingForMemberships = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    public function cloneEntity()
    {
        $newProduct = parent::cloneEntity();

        if ($this->getFreeShippingForMemberships()->count()) {
            foreach ($this->getFreeShippingForMemberships() as $membership) {
                $newProduct->addFreeShippingForMemberships($membership);
            }
        }

        return $newProduct;
    }

}