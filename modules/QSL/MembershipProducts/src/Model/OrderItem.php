<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MembershipProducts\Model;

use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender;

/**
 * Order item model with membership assignements
 * @Extender\Mixin
 */
class OrderItem extends \XLite\Model\OrderItem
{
    /**
     * 'Customer membership is applied' flag
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $customerMembershipApplied = false;

    /**
     * Customer membership assigned date
     *
     * @var integer
     *
     * @ORM\Column (type="integer", nullable=true, options={ "unsigned": true })
     */
    protected $customerMembershipAssignDate;

    /**
     * Customer membership unassigned date
     *
     * @var integer
     *
     * @ORM\Column (type="integer", nullable=true, options={ "unsigned": true })
     */
    protected $customerMembershipUnassignDate;

    /**
     * Old customer's membership before assignment
     *
     * @var \XLite\Model\Membership
     *
     * @ORM\OneToOne (targetEntity="XLite\Model\Membership", fetch="LAZY", cascade={"all"})
     * @ORM\JoinColumn (name="old_customer_membership_id", referencedColumnName="membership_id", onDelete="SET NULL")
     */
    protected $oldCustomerMembership;

    /**
     * Prepare membership change record for history
     *
     * @param \XLite\Model\Profile    $origProfile
     * @param \XLite\Model\Membership $newMembership
     *
     * @return array
     */
    protected function getMembershipChangeRecordForHistory(\XLite\Model\Profile $origProfile, \XLite\Model\Membership $newMembership = null)
    {
        $change = [
            'oldMembership' => (string) static::t('Ignore membership'),
            'newMembership' => (string) static::t('Ignore membership'),
        ];

        if ($newMembership) {
            $change['newMembership'] = $newMembership->getName();
        }

        $currentMembership = $origProfile->getMembership();

        if ($currentMembership) {
            $change['oldMembership'] = $currentMembership->getName();
        }

        return $change;
    }

    /**
     * Can apply membership to customer or not
     *
     * @return boolean
     */
    public function canApplyMembershipToCustomer()
    {
        $result = false;

        /** @var \QSL\MembershipProducts\Model\Product $product */
        $product = $this->getProduct();
        /** @var \QSL\MembershipProducts\Model\Order $order */
        $order = $this->getOrder();

        if ($product && $order) {
            $origProfile = $order->getOrigProfile();

            $conditions = [
                'getUniqueIdentifier'               => (bool) $product->getUniqueIdentifier(),
                'getAppointmentMembership'          => (bool) $product->getAppointmentMembership(),
                'getEnabled'                        => (bool) ($product->getAppointmentMembership()
                    ? $product->getAppointmentMembership()->getEnabled()
                    : false),
                'getOrigProfile'                    => (bool) $origProfile,
                'hasOpenedAssignedMemberships'      => (bool) ($origProfile
                    ? $origProfile->hasOpenedAssignedMemberships()
                    : false),
                'getCustomerMembershipApplied'      => (bool) !$this->getCustomerMembershipApplied(),
                'getCustomerMembershipAssignDate'   => (bool) !$this->getCustomerMembershipAssignDate(),
                'getCustomerMembershipUnassignDate' => (bool) !$this->getCustomerMembershipUnassignDate(),
            ];

            $result = array_sum($conditions) === count($conditions);

            if (!$result) {
                $this->getLogger('QSL-MembershipProducts')->error('canApplyMembershipToCustomer, failed conditions: ' . json_encode(['order' => $order->getOrderNumber(), 'conditions' => $conditions]));
            }
        }

        return $result;
    }

    /**
     * Apply membership to customer
     *
     * @return boolean
     */
    public function applyMembershipToCustomer()
    {
        $result = false;

        /** @var \QSL\MembershipProducts\Model\Product $product */
        $product = $this->getProduct();
        /** @var \QSL\MembershipProducts\Model\Order $order */
        $order = $this->getOrder();

        if ($product && $order) {
            $origProfile = $order->getOrigProfile();

            if ($origProfile) {
                $appointmentMembership = $product->getAppointmentMembership();

                $this->setOldCustomerMembership($origProfile->getMembership());

                $change = $this->getMembershipChangeRecordForHistory(
                    $origProfile,
                    $appointmentMembership
                );

                $origProfile->setMembership($appointmentMembership);

                $this->setCustomerMembershipApplied(true);
                $this->setCustomerMembershipAssignDate(\XLite\Core\Converter::time());
                $this->setCustomerMembershipUnassignDate($this->getMembershipExpirationTime());

                \XLite\Core\OrderHistory::getInstance()->registerOrderMembershipProductChange(
                    $order->getOrderId(),
                    $change
                );

                \XLite\Core\Mailer::getInstance()->sendAssignedMembershipProductNotification($this);

                $result = true;
            }

            if (!$result) {
                $this->getLogger('QSL-MembershipProducts')->error('applyMembershipToCustomer, failed conditions: ' . json_encode(['order' => $order->getOrderNumber(), 'origProfile' => $origProfile]));
            }
        }

        return $result;
    }

    /**
     * Reset customer's membership
     *
     * @return boolean
     */
    public function resetCustomerMembership()
    {
        $result = false;

        /** @var \QSL\MembershipProducts\Model\Product $product */
        $product = $this->getProduct();
        /** @var \QSL\MembershipProducts\Model\Order $order */
        $order = $this->getOrder();

        if (
            $product
            && $product->getUniqueIdentifier()
            && $this->getCustomerMembershipApplied()
            && $order
            && ($origProfile = $order->getOrigProfile())
        ) {
            $oldCustomerMembership = $this->getOldCustomerMembership();

            $change = $this->getMembershipChangeRecordForHistory($origProfile, $oldCustomerMembership);

            $origProfile->setMembership($oldCustomerMembership);

            $this->setCustomerMembershipApplied(false);
            $this->setCustomerMembershipAssignDate(null);
            $this->setCustomerMembershipUnassignDate(null);

            \XLite\Core\OrderHistory::getInstance()->registerOrderMembershipProductChange(
                $order->getOrderId(),
                $change
            );

            \XLite\Core\Mailer::getInstance()->sendResetMembershipProductNotification($this);

            $result = true;
        }

        return $result;
    }

    /**
     * Check - assigned membership is expired or not
     *
     * @return boolean
     */
    public function isAssignedMembershipExpired()
    {
        /** @var \QSL\MembershipProducts\Model\Product $product */
        $product = $this->getProduct();

        $result = false;
        if (
            $product
            && $product->getUniqueIdentifier()
            && $product->getAssignedMembershipTTLType() != \XLite\Model\Product::MEMBERSHIP_TTL_TYPE_NONE
            && $this->getCustomerMembershipApplied()
        ) {
            $end = $this->getMembershipExpirationTime();
            if ($end && $end < \XLite\Core\Converter::time()) {
                $result = true;
            }
        }

        return $result;
    }

    /**
     * Get membership expiration time
     *
     * @param integer $start Start date
     *
     * @return integer|null
     */
    public function getMembershipExpirationTime($start = null)
    {
        /** @var \QSL\MembershipProducts\Model\Product $product */
        $product = $this->getProduct();

        $end = null;
        if ($product && $product->getUniqueIdentifier()) {
            $start = $start ?: $this->getCustomerMembershipAssignDate();
            switch ($product->getAssignedMembershipTTLType()) {
                case \XLite\Model\Product::MEMBERSHIP_TTL_TYPE_DAY:
                    $end = $start + ($product->getAssignedMembershipTTL() * 86400 * $this->getAmount());
                    break;

                case \XLite\Model\Product::MEMBERSHIP_TTL_TYPE_WEEK:
                    $end = $start + ($product->getAssignedMembershipTTL() * 86400 * 7 * $this->getAmount());
                    break;

                case \XLite\Model\Product::MEMBERSHIP_TTL_TYPE_MONTH:
                    $end = strtotime('+' . ($product->getAssignedMembershipTTL() * $this->getAmount()) . ' months', $start);
                    break;

                case \XLite\Model\Product::MEMBERSHIP_TTL_TYPE_YEAR:
                    $end = strtotime('+' . ($product->getAssignedMembershipTTL() * $this->getAmount()) . ' years', $start);
                    break;

                case \XLite\Model\Product::MEMBERSHIP_TTL_TYPE_NONE:
                    $end = pow(2, 32) >> 1;
                    break;

                default:
            }
        }

        return $end;
    }

    /**
     * Set customerMembershipApplied
     *
     * @param boolean $customerMembershipApplied
     *
     * @return static
     */
    public function setCustomerMembershipApplied($customerMembershipApplied)
    {
        $this->customerMembershipApplied = $customerMembershipApplied;

        return $this;
    }

    /**
     * Get customerMembershipApplied
     *
     * @return boolean
     */
    public function getCustomerMembershipApplied()
    {
        return $this->customerMembershipApplied;
    }

    /**
     * Set customerMembershipAssignDate
     *
     * @param integer $customerMembershipAssignDate
     *
     * @return static
     */
    public function setCustomerMembershipAssignDate($customerMembershipAssignDate)
    {
        $this->customerMembershipAssignDate = $customerMembershipAssignDate;

        return $this;
    }

    /**
     * Get customerMembershipAssignDate
     *
     * @return integer
     */
    public function getCustomerMembershipAssignDate()
    {
        return $this->customerMembershipAssignDate;
    }

    /**
     * Set customerMembershipUnassignDate
     *
     * @param integer $customerMembershipUnassignDate
     *
     * @return static
     */
    public function setCustomerMembershipUnassignDate($customerMembershipUnassignDate)
    {
        $this->customerMembershipUnassignDate = $customerMembershipUnassignDate;

        return $this;
    }

    /**
     * Get customerMembershipUnassignDate
     *
     * @return integer
     */
    public function getCustomerMembershipUnassignDate()
    {
        return $this->customerMembershipUnassignDate;
    }

    /**
     * Set oldCustomerMembership
     *
     * @param \XLite\Model\Membership $oldCustomerMembership
     *
     * @return static
     */
    public function setOldCustomerMembership(\XLite\Model\Membership $oldCustomerMembership = null)
    {
        $this->oldCustomerMembership = $oldCustomerMembership;

        return $this;
    }

    /**
     * Get oldCustomerMembership
     *
     * @return \XLite\Model\Membership
     */
    public function getOldCustomerMembership()
    {
        return $this->oldCustomerMembership;
    }
}
