<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MembershipProducts\Model\DTO\Product;

use XCart\Extender\Mapping\Extender;

/**
 * Product
 * @Extender\Mixin
 */
class Info extends \XLite\Model\DTO\Product\Info
{
    /**
     * @param \XLite\Model\Product $object Product
     */
    protected function init($object)
    {
        parent::init($object);

        $this->default->appointmentMembership = $object->getAppointmentMembership()
            ? $object->getAppointmentMembership()->getMembershipId()
            : 0;

        $this->default->assignedMembershipTTLType = [
            'type'  => $object->getAssignedMembershipTTLType(),
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

        $membership = $default->appointmentMembership
            ? \XLite\Core\Database::getRepo('XLite\Model\Membership')->find($default->appointmentMembership)
            : null;

        $object->setAppointmentMembership($membership)
            ->setAssignedMembershipTTL(max(1, $default->assignedMembershipTTLType['value']))
            ->setAssignedMembershipTTLType($default->assignedMembershipTTLType['type']);
    }
}
