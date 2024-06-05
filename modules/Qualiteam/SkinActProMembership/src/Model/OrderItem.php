<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActProMembership\Model;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;
use XLite\Core\Auth;
use XLite\Core\Database;

/**
 * @Extender\Mixin
 * @Extender\After ({"CDev\Wholesale", "QSL\MembershipProducts"})
 */
class OrderItem extends \XLite\Model\OrderItem
{

    /**
     * Customer membership expiration warning sent date
     *
     * @var integer
     *
     * @ORM\Column (type="integer", nullable=true, options={ "unsigned": true })
     */
    protected $customerMembershipExpirationSentDate;


    /**
     * Customer membership unassigned date
     *
     * we need SIGNED VALUE here
     *
     * @var integer
     *
     * @ORM\Column (type="integer", nullable=true)
     */
    protected $customerMembershipUnassignDate;


    /**
     * @return int
     */
    public function getCustomerMembershipExpirationSentDate()
    {
        return $this->customerMembershipExpirationSentDate;
    }

    /**
     * @param int $customerMembershipExpirationSentDate
     */
    public function setCustomerMembershipExpirationSentDate($customerMembershipExpirationSentDate)
    {
        $this->customerMembershipExpirationSentDate = $customerMembershipExpirationSentDate;
    }

    public function setWholesaleValues()
    {
        $pid = (int)\XLite\Core\Config::getInstance()->Qualiteam->SkinActProMembership->product_to_add;

        if ($pid > 0 && $this->getOrder()) {

            $paidMembershipInCart = false;
            $paidMembershipProduct = false;

            foreach ($this->getOrder()->getItems() as $item) {
                if ($item->getProduct()->getProductId() === $pid) {
                    $paidMembershipInCart = true;
                    $paidMembershipProduct = $item->getProduct();
                    break;
                }
            }

            if ($paidMembershipInCart
                && $this->getProduct()->getProductId() !== $pid
            ) {
                $this->getProduct()->setWholesaleMembership($paidMembershipProduct->getAppointmentMembership());

                return;
            }

        }

        return parent::setWholesaleValues();
    }

    public function setAmount($amount)
    {
        parent::setAmount($amount);

        $pid = (int)\XLite\Core\Config::getInstance()->Qualiteam->SkinActProMembership->product_to_add;

        if ($this->getAmount() > 1
            && $this->getProduct()->getProductId() === $pid
        ) {
            $this->setAmount(1);
        }
    }

    public function canApplyMembershipToCustomer()
    {
        $result = parent::canApplyMembershipToCustomer();

        return $result;
    }

    public function getOldCustomerMembership()
    {
        return null; // always reset to nothing
    }

    public function applyMembershipToCustomer()
    {
        $result = parent::applyMembershipToCustomer();

        // applied, deactivate old items
        if ($result) {

            $order = $this->getOrder();

            $origProfile = $order->getOrigProfile();

            // if same memberships
            $proItemsApplied = Database::getRepo('\XLite\Model\OrderItem')
                ->findAnyProfileAppliedMemberships($origProfile);

            if ($proItemsApplied) {

                foreach ($proItemsApplied as $appliedItem) {

                    if ($appliedItem === $this) {
                        continue;
                    }

                    $product = $this->getProduct();
                    $prevProduct = $appliedItem->getProduct();

                    if ($product
                        && $prevProduct
                        && $product->getAppointmentMembership() === $prevProduct->getAppointmentMembership()
                    ) {
                        // same membership - correct dates
                        $this->setCustomerMembershipAssignDate($appliedItem->getCustomerMembershipAssignDate());
                        $newUnassignDate = $this->getMembershipExpirationTime($appliedItem->getCustomerMembershipUnassignDate());
                        $this->setCustomerMembershipUnassignDate($newUnassignDate);
                    }

                    // reset previously applied item
                    $appliedItem->setCustomerMembershipApplied(false);
                    $appliedItem->setCustomerMembershipAssignDate(null);
                    $appliedItem->setCustomerMembershipUnassignDate(null);
                }
            }

        }

        return $result;
    }

}