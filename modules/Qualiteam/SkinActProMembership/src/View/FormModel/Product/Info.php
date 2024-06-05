<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace Qualiteam\SkinActProMembership\View\FormModel\Product;


use XCart\Extender\Mapping\Extender;
use XLite\Core\Database;
use XLite\View\FormModel\Type\SwitcherType;

/**
 * Product view model
 * @Extender\Mixin
 * @Extender\After ("QSL\MembershipProducts")
 */
class Info extends \XLite\View\FormModel\Product\Info
{
    protected function defineFields()
    {
        $schema = parent::defineFields();

        unset($schema['default']['appointmentMembership']['choices']['None']);

        $schema['default']['paidMembership'] = [
            'label' => static::t('SkinActProMembership Product is a paid membership'),
            'type' => SwitcherType::class,
            'position' => 6903,
        ];

        $schema['default']['appointmentMembership']['show_when'] = [
            'default' => [
                'paidMembership' => true,
            ],
        ];

        $schema['default']['assignedMembershipTTLType']['show_when'] = [
            'default' => [
                'paidMembership' => true,
            ],
        ];

        $schema['default']['appointmentMembership']['label'] = static::t('SkinActProMembership product field membership');
        $schema['default']['assignedMembershipTTLType']['label'] = static::t('SkinActProMembership product field duration');


        $memberships = Database::getRepo('\XLite\Model\Membership')->findAll();

        $membershipChoices = [];

        foreach ($memberships as $membership) {
            $membershipChoices[$membership->getMembershipId()] = $membership->getName();
        }

        // Free shipping for memberships selectbox
        $schema['default']['freeShippingForMemberships'] = [
            'label' => static::t('SkinActProMembership free_shipping_for_memberships'),
            'type' => 'Symfony\Component\Form\Extension\Core\Type\ChoiceType',
            'choices' => array_flip($membershipChoices),
            'placeholder' => false,
            'position' => 6901,
            'multiple' => true,
            // 'data' => []
        ];


        $schema['default']['freeShippingStamp'] = [
            'label' => static::t('SkinActProMembership Show free shipping stamp'),
            'type' => SwitcherType::class,
            'position' => 6902,
        ];

        // filter appointment memberships

        $appointmentMemberships = [
            (string)static::t('None') => 0,
        ];

        $qb = Database::getRepo('\XLite\Model\Membership')->createPureQueryBuilder();

        $qb->linkLeft('\XLite\Model\Product', 'p', 'WITH', 'm.membership_id = p.appointmentMembership')
            ->where('p.product_id IS NULL');

        foreach ($qb->getResult() as $appointmentMembership) {
            $appointmentMemberships[$appointmentMembership->getName()] = $appointmentMembership->getMembershipId();
        }

        // add current product appointment membership if exists
        if ($this->getDataObject()->default->appointmentMembership > 0) {
            $currentMembership = Database::getRepo('\XLite\Model\Membership')
                ->find($this->getDataObject()->default->appointmentMembership);
            $appointmentMemberships[$currentMembership->getName()] = $currentMembership->getMembershipId();
        }

        $schema['default']['appointmentMembership']['choices'] = $appointmentMemberships;

        return $schema;
    }

    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/Qualiteam/SkinActProMembership/js/switcher_fix.js';
        return $list;
    }
}