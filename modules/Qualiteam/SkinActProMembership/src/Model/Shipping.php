<?php


namespace Qualiteam\SkinActProMembership\Model;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Auth;


/**
 * @Extender\Mixin
 */
class Shipping extends \XLite\Model\Shipping
{
    protected function isFreeShippingByMembership($membership, $order)
    {
        $freeShipping = true;

        foreach ($order->getItems() as $item) {
            // that product has free shipping setting for that membership
            // or that product is a paid membership product
            $freeShipping =
                $freeShipping
                && (
                    $item->getProduct()->getFreeShippingForMemberships()->contains($membership)
                    || $item->getProduct()->getAppointmentMembership()
                );

            if (!$freeShipping) {
                break;
            }
        }

        return $freeShipping;
    }

    protected function filterRatesByMembership($membership, $order, $rates)
    {
        $originalCount = count($rates);

        if ($this->isFreeShippingByMembership($membership, $order)) {

            $proShippingMethodId = (int)\XLite\Core\Config::getInstance()->Qualiteam->SkinActProMembership->pro_shipping_method;

            foreach ($rates as $ind => $rate) {
                if ($rate->getMethod()->getMethodId() !== $proShippingMethodId) {
                    unset($rates[$ind]);
                }
            }

// old filtration method
//            foreach ($rates as $ind => $rate) {
//                if ($rate->getMarkupRate() > 0) {
//                    // preserve free shipping rates only
//                    unset($rates[$ind]);
//                }
//            }

            // preserve "Free shipping" rate only if exists
//            $freeShippingMethodExists = false;
//
//            foreach ($rates as $ind => $rate) {
//                if ($rate->getMethod()->getName() === 'Free shipping') {
//                    $freeShippingMethodExists = true;
//                    break;
//                }
//            }
//
//            if ($freeShippingMethodExists) {
//                foreach ($rates as $ind => $rate) {
//                    if ($rate->getMethod()->getName() !== 'Free shipping') {
//                        unset($rates[$ind]);
//                    }
//                }
//            }


        }

        return [$rates, $originalCount !== count($rates)];
    }

    protected function getProcessorRates($processor, $modifier)
    {
        $rates = parent::getProcessorRates($processor, $modifier);

        $membership = Auth::getInstance()->getMembership();

        $modified = false;

        $order = $modifier->getOrder();

        if ($membership) {
            [$rates, $modified] = $this->filterRatesByMembership($membership, $order, $rates);
        }

        if (!$modified) {

            // lets see if order has paid membership product
            $pid = (int)\XLite\Core\Config::getInstance()->Qualiteam->SkinActProMembership->product_to_add;

            if ($pid > 0) {

                $paidMembershipInCart = false;
                $paidMembershipProduct = false;

                foreach ($order->getItems() as $item) {
                    if ($item->getProduct()->getProductId() === $pid) {
                        $paidMembershipInCart = true;
                        $paidMembershipProduct = $item->getProduct();
                        break;
                    }
                }

                if ($paidMembershipInCart) {
                    $membership = $paidMembershipProduct->getAppointmentMembership();
                    if ($this->isFreeShippingByMembership($membership, $order)) {
                        $modified = true;
                        // all methods becomes free here
                        foreach ($rates as $rate) {
                            $rate->setMarkupRate(0);
                            $rate->getMethod()->setFreeByMembership(true);
                        }

                    }


                }

            }
        }

        if (!$modified) {
            // unset special free shipping method
            $proShippingMethodId = (int)\XLite\Core\Config::getInstance()->Qualiteam->SkinActProMembership->pro_shipping_method;

            foreach ($rates as $ind => $rate) {
                if ($rate->getMethod()->getMethodId() === $proShippingMethodId) {
                    unset($rates[$ind]);
                }
            }
        }

        return $rates;
    }

}