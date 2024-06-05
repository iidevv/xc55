<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsConnector\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * Cart repository
 *
 * @Extender\Mixin
 */
class Cart extends \XLite\Model\Repo\Cart
{
    /**
     * Find carts by payment method names 
     *
     * @param array $names List of payment method names
     *
     * @return Cart[]
     */
    public function findByPaymentMethodNames($names)
    {
        if (!is_array($names)) {
            $names = array($names);
        }

        $qb = $this->createQueryBuilder()
            ->andWhere('c.payment_method_name IN (:paymentMethodNames)')
            ->setParameter('paymentMethodNames', $names);

        return $qb->getResult();
    }

    /**
     * Alter default findOneByProfile() method to select real cart only
     *
     * @param \XLite\Model\Profile $profile Profile object
     *
     * @return \XLite\Model\Cart
     */
    public function findOneByProfile($profile)
    {
        return parent::findOneBy(array('profile' => $profile, 'is_zero_auth' => false));
    }
}
