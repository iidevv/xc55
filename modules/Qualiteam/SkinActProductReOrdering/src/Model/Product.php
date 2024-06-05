<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActProductReOrdering\Model;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Auth;
use XLite\Core\Cache\ExecuteCachedTrait;
use XLite\Core\Database;
use XLite\Model\OrderItem;
use XLite\Model\Profile;

/**
 * @Extender\Mixin
 */
class Product extends \XLite\Model\Product
{
    use ExecuteCachedTrait;
    public function getLastOrderItem()
    {
        $profile = Auth::getInstance()->getProfile();

        return $this->executeCachedRuntime(function () use ($profile) {
            return $this->getLastOrderItemDB($profile);
        }, [
            __METHOD__,
            self::class,
            $profile->getProfileId(),
            $this->getProductId(),
        ]);
    }

    public function getLastOrderItemDB(Profile $profile)
    {
        return Database::getRepo(OrderItem::class)
            ->getLastOrderItem($profile, $this);
    }
}