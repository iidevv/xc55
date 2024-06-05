<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActProMembership\View\BuyProMembership;

use Qualiteam\SkinActProMembership\View\FormField\Select\ProductToAddSelect;
use XLite\View\Button\AButton;
use XLite\View\Button\Regular;

class BuyProMembershipModel extends \XLite\View\Model\AModel
{
    public function __construct($params = [], $sections = [])
    {
        $this->schemaDefault = [
            'product_id' => [
                self::SCHEMA_CLASS => ProductToAddSelect::class,
            ],
        ];

        parent::__construct($params, $sections);
    }

    protected function getDefaultModelObject()
    {

    }

    protected function getFormClass()
    {
        return BuyProMembershipForm::class;
    }

    protected function getFormButtons()
    {
        $result = parent::getFormButtons();

        $result['submit'] = new \XLite\View\Button\Submit(
            [
                AButton::PARAM_LABEL    => static::t('SkinActProMembership choose pro membership'),
                AButton::PARAM_BTN_TYPE => 'regular-main-button',
                AButton::PARAM_STYLE    => 'action',
            ]
        );

        $result['cancel'] = new Regular(
            [
                AButton::PARAM_LABEL    => static::t('Cancel'),
                AButton::PARAM_BTN_TYPE => 'regular-button',
                AButton::PARAM_STYLE    => 'action always-enabled',
                Regular::PARAM_JS_CODE  => 'popup.close();',
            ]
        );

        return $result;
    }

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/Qualiteam/SkinActProMembership/buy_pro_membership.less';

        return $list;
    }
}