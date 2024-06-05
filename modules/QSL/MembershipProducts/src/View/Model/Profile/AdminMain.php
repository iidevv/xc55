<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MembershipProducts\View\Model\Profile;

use XCart\Extender\Mapping\Extender;

/**
 * Administrator profile model widget
 * @Extender\Mixin
 */
class AdminMain extends \XLite\View\Model\Profile\AdminMain
{
    protected function getFormFieldsForSectionAccess()
    {
        // Add extra details if membership was applied automatically
        if ($this->getItemsAppliedProfileMembershipExpirationDate()) {
            $pos = array_search(
                'membership_id',
                array_keys($this->accessSchema)
            ) + 1;

            $before_membership = array_slice($this->accessSchema, 0, $pos);
            $after_membership  = array_slice($this->accessSchema, $pos);

            $this->accessSchema = $before_membership
                + $this->getMembershipProductsAccessSchema()
                + $after_membership;
        }

        return parent::getFormFieldsForSectionAccess();
    }

    protected function getMembershipProductsAccessSchema()
    {
        return [
            'customerMembershipAssignDate'   => [
                self::SCHEMA_CLASS    => '\XLite\View\FormField\Label',
                self::SCHEMA_LABEL    => 'Membership assigned date',
                self::SCHEMA_REQUIRED => false,
            ],
            'customerMembershipUnassignDate' => [
                self::SCHEMA_CLASS    => '\XLite\View\FormField\Label',
                self::SCHEMA_LABEL    => 'Membership expiration date',
                self::SCHEMA_REQUIRED => false,
            ],
        ];
    }

    /**
     * Returns items applied profile membership expiration date
     *
     * @return array|null
     */
    protected function getItemsAppliedProfileMembershipExpirationDate()
    {
        static $items = null;

        if ($items === null) {
            $items = [];

            $profile = $this->getModelObject();

            if (
                $profile
                && $profile->isPersistent()
            ) {
                $foundItems = \XLite\Core\Database::getRepo('\XLite\Model\OrderItem')
                    ->findItemsAppliedProfileMembershipExpirationDate($profile);

                foreach ($foundItems as $item) {
                    if ($item->customerMembershipAssignDate > 0) {
                        $items[] = $item;
                    }
                }
            }
        }

        return $items;
    }

    public function getDefaultFieldValue($name)
    {
        $value = parent::getDefaultFieldValue($name);

        switch ($name) {
            case 'customerMembershipAssignDate':
                $value = false;
                foreach ($this->getItemsAppliedProfileMembershipExpirationDate() as $item) {
                    if ($item->customerMembershipAssignDate > 0) {
                        $value = $item->customerMembershipAssignDate;
                        break;
                    }
                }
                $value = ($value === false
                    ? ''
                    : \XLite\Core\Converter::formatTime($value));
                break;
            case 'customerMembershipUnassignDate':
                $value = false;
                foreach ($this->getItemsAppliedProfileMembershipExpirationDate() as $item) {
                    if ($item->customerMembershipUnassignDate > 0) {
                        $value = $item->customerMembershipUnassignDate;
                        break;
                    }
                }
                $value = ($value === false
                    ? ''
                    : \XLite\Core\Converter::formatTime($value));
                break;
        }

        return $value;
    }
}
