<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MembershipProducts\View\FormModel\Product;

use XCart\Extender\Mapping\Extender;

/**
 * Product view model
 * @Extender\Mixin
 */
class Info extends \XLite\View\FormModel\Product\Info
{
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = 'modules/QSL/MembershipProducts/form_model/style.css';

        return $list;
    }

    public function getJSFiles()
    {
        $list   = parent::getJSFiles();
        $list[] = 'modules/QSL/MembershipProducts/form_model/controller.js';

        return $list;
    }

    protected function defineFields()
    {
        $schema = parent::defineFields();

        $memberships = [
            (string) static::t('None') => 0,
        ];

        foreach (\XLite\Core\Database::getRepo('XLite\Model\Membership')->findActiveMemberships() as $membership) {
            $memberships[$membership->getName()] = $membership->getMembershipId();
        }

        $schema[static::SECTION_DEFAULT]['appointmentMembership'] = [
            'label'       => (string) static::t('Membership to assign to product purchaser'),
            'type'        => 'Symfony\Component\Form\Extension\Core\Type\ChoiceType',
            'choices'     => $memberships,
            'placeholder' => false,
            'position'    => 7000,
        ];

        $schema[static::SECTION_DEFAULT]['assignedMembershipTTLType'] = [
            'label'    => (string) static::t('Membership duration'),
            'type'     => 'QSL\MembershipProducts\View\FormModel\Type\MembershipType',
            'position' => 7010,
        ];

        return $schema;
    }
}
