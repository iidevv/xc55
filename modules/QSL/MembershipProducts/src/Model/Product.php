<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MembershipProducts\Model;

use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender;

/**
 * The "product" model class
 * @Extender\Mixin
 */
class Product extends \XLite\Model\Product
{
    /**
     * TTL type codes
     */
    public const MEMBERSHIP_TTL_TYPE_NONE  = '';
    public const MEMBERSHIP_TTL_TYPE_DAY   = 'D';
    public const MEMBERSHIP_TTL_TYPE_WEEK  = 'W';
    public const MEMBERSHIP_TTL_TYPE_MONTH = 'M';
    public const MEMBERSHIP_TTL_TYPE_YEAR  = 'Y';

    /**
     * Assigned membership TTL type
     *
     * @var string
     *
     * @ORM\Column (type="string", length=1, options={ "fixed": true })
     */
    protected $assignedMembershipTTLType = self::MEMBERSHIP_TTL_TYPE_NONE;

    /**
     * Assign membership TTL
     *
     * @var integer
     *
     * @ORM\Column (type="integer", options={ "unsigned": true })
     */
    protected $assignedMembershipTTL = 1;

    /**
     * Membership for appointment to the user
     *
     * @var \XLite\Model\Membership
     *
     * @ORM\ManyToOne (targetEntity="XLite\Model\Membership", fetch="LAZY")
     * @ORM\JoinColumn (name="appointment_membership_id", referencedColumnName="membership_id", onDelete="SET NULL")
     */
    protected $appointmentMembership;

    /**
     * Get assigned membership TTL
     *
     * @return integer
     */
    public function getAssignedMembershipTTL()
    {
        return max(1, $this->assignedMembershipTTL);
    }

    /**
     * Set assignedMembershipTTLType
     *
     * @param string $assignedMembershipTTLType
     *
     * @return Product
     */
    public function setAssignedMembershipTTLType($assignedMembershipTTLType)
    {
        $this->assignedMembershipTTLType = $assignedMembershipTTLType;

        return $this;
    }

    /**
     * Get assignedMembershipTTLType
     *
     * @return string
     */
    public function getAssignedMembershipTTLType()
    {
        return $this->assignedMembershipTTLType;
    }

    /**
     * Set assignedMembershipTTL
     *
     * @param integer $assignedMembershipTTL
     *
     * @return Product
     */
    public function setAssignedMembershipTTL($assignedMembershipTTL)
    {
        $this->assignedMembershipTTL = $assignedMembershipTTL;

        return $this;
    }

    /**
     * Set appointmentMembership
     *
     * @param \XLite\Model\Membership $appointmentMembership
     *
     * @return Product
     */
    public function setAppointmentMembership(\XLite\Model\Membership $appointmentMembership = null)
    {
        $this->appointmentMembership = $appointmentMembership;

        return $this;
    }

    /**
     * Get appointmentMembership
     *
     * @return \XLite\Model\Membership
     */
    public function getAppointmentMembership()
    {
        return $this->appointmentMembership;
    }
}
