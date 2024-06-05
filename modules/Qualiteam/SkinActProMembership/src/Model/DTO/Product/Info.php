<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActProMembership\Model\DTO\Product;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Database;

/**
 * Product
 * @Extender\Mixin
 * @Extender\After ("QSL\MembershipProducts")
 */
class Info extends \XLite\Model\DTO\Product\Info
{
    /**
     * @param \XLite\Model\Product $object Product
     */
    protected function init($object)
    {
        parent::init($object);

        $this->default->paidMembership = $object->getPaidMembership();
        $this->default->freeShippingForMemberships = $object->freeShippingForMemberships();
        $this->default->freeShippingStamp = $object->getShowFreeShippingStamp();

        $this->default->assignedMembershipTTLType = [
            'type'  => $object->getAssignedMembershipTTLType() ?: \XLite\Model\Product::MEMBERSHIP_TTL_TYPE_DAY,
            'value' => $object->getAssignedMembershipTTL(),
        ];
    }

    /**
     * @param \XLite\Model\Product $object Product
     */
    public function populateTo($object, $rawData = null)
    {
        parent::populateTo($object, $rawData);

        $default = $this->default;

        $object->setPaidMembership($default->paidMembership);

        if ($default->freeShippingForMemberships) {
            $memberships = Database::getRepo('\XLite\Model\Membership')->findByIds($default->freeShippingForMemberships);
            $object->setFreeShippingForMemberships($memberships);
        } else {
            $object->setFreeShippingForMemberships([]);
        }

        $object->setShowFreeShippingStamp($default->freeShippingStamp);

    }
}
