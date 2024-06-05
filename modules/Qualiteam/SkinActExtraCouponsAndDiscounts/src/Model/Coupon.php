<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActExtraCouponsAndDiscounts\Model;

use Doctrine\ORM\Mapping as ORM;
use Qualiteam\SkinActProMembership\Helpers\Profile;
use XCart\Extender\Mapping\Extender as Extender;

/**
 * @Extender\Mixin
 *
 * @ORM\Table(indexes={
 *         @ORM\Index (name="extraCoupon", columns={"extraCoupon"})
 *     })
 *
 * @ORM\HasLifecycleCallbacks
 * @ORM\MappedSuperclass
 */
class Coupon extends \CDev\Coupons\Model\Coupon
{
    /**
     * @var \Qualiteam\SkinActExtraCouponsAndDiscounts\Model\ExtraCouponsAndDiscounts
     *
     * @ORM\OneToOne   (targetEntity="Qualiteam\SkinActExtraCouponsAndDiscounts\Model\ExtraCouponsAndDiscounts")
     * @ORM\JoinColumn (name="extraCoupon", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $extraCoupon;

    public function getExtraCoupon()
    {
        return $this->extraCoupon;
    }

    public function setExtraCoupon($extraCoupon)
    {
        $this->extraCoupon = $extraCoupon;
    }

    protected function checkMembership(\XLite\Model\Order $order)
    {
        $profile = $order->getProfile();
        if (!$profile
            || (
                $this->getExtraCoupon()
                && !(new Profile($profile))->isProfileProMembership()
            )
        ) {
            $this->throwCompatibilityException(
                '',
                static::t('SkinActExtraCouponsAndDiscounts sorry the coupon you entered is not valid for your membership level contact the administrator')
            );
        } else {
            parent::checkMembership($order);
        }

    }
}