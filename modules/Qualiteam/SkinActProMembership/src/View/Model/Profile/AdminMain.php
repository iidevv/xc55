<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActProMembership\View\Model\Profile;

use Qualiteam\SkinActProMembership\Helpers\ProMembershipProducts;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Converter;
use XLite\Model\Profile;
use XLite\View\Button\AButton;

/**
 * @Extender\Mixin
 */
class AdminMain extends \XLite\View\Model\Profile\AdminMain
{
    public function __construct(array $params = [], array $sections = [])
    {
        parent::__construct($params, $sections);

        if ($this->getModelObject() instanceof Profile
            && $this->getModelObject()->getLastProMembershipEmail() > 0
        ) {
            $this->accessSchema['membership_id'][self::SCHEMA_COMMENT] = static::t('SkinActProMembership last email about buying pro membership was', [
                'time' => Converter::formatTime($this->getModelObject()->getLastProMembershipEmail())
            ]);
        }
    }

    protected function getFormButtons()
    {
        $result = parent::getFormButtons();

        if ($this->checkProfileForProMembership()
            && ProMembershipProducts::getProMembersProductsCount() > 0
        ) {
            $result = $this->prepareBuyProMembershipButton($result);
        }

        return $result;
    }

    protected function checkProfileForProMembership()
    {
        return $this->getModelObject()
            && $this->getModelObject()->getProfileId()
            && !$this->getModelObject()->getMembershipId()
            && !$this->getModelObject()->isAdmin();
    }

    protected function prepareBuyProMembershipButton(array $buttons)
    {
        $buttons['buy-pro-membership'] = new \XLite\View\Button\Simple(
            [
                AButton::PARAM_LABEL    => ProMembershipProducts::getProMembersProductsCount() > 1
                    ? static::t('SkinActProMembership Buy membership')
                    : static::t('SkinActProMembership Buy pro membership'),
                AButton::PARAM_BTN_TYPE => 'regular-button model-button always-enabled',
                AButton::PARAM_STYLE    => 'buy-pro-membership-button',
                AButton::PARAM_ATTRIBUTES => [
                    'data-product-id' => ProMembershipProducts::getProMembersProductsCount() === 1 ? ProMembershipProducts::getProMembershipProduct()->getProductId() : 0,
                    'data-opening-popup' => ProMembershipProducts::getProMembersProductsCount() > 1,
                ],
            ]
        );

        return $buttons;
    }

    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/Qualiteam/SkinActProMembership/js/buyProMembershipButton.js';

        return $list;
    }
}