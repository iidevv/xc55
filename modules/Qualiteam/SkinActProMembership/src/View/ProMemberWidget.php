<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActProMembership\View;

use XCart\Extender\Mapping\ListChild;

/**
 * ProMember dropdown widget
 *
 * @ListChild(list="layout.header.widgets", weight="20", zone="customer")
 * */
class ProMemberWidget extends \XLite\View\AView
{
    protected function getDefaultTemplate()
    {
        return 'modules/Qualiteam/SkinActProMembership/pro_member_widget/body.twig';
    }

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = [
            'file'  => 'modules/Qualiteam/SkinActProMembership/css/less/pro_member_widget.less',
            'media' => 'screen',
            'merge' => 'bootstrap/css/bootstrap.less',
        ];
        return $list;
    }

    protected function getProMembershipUrlLink()
    {
        return \XLite\Core\Config::getInstance()->Qualiteam->SkinActProMembership->pro_membership_link_url
            ? \XLite\Core\Config::getInstance()->Qualiteam->SkinActProMembership->pro_membership_link_url
            : 'https://www.spaandequipment.com/Professional-Membership-Pro-Member.html';
    }

}