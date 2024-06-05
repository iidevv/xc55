<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActProMembership\View;


use Qualiteam\SkinActProMembership\View\FormField\Select\OPCPaidMembershipSelect;

class OPCPaidMembership extends \XLite\View\AView
{
    protected function isVisible()
    {
        return OPCPaidMembershipSelect::isVisibleStatic() && parent::isVisible();
    }

    protected function getDefaultTemplate()
    {
        return 'modules/Qualiteam/SkinActProMembership/opc_paid_membership_select.twig';
    }

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = [
            'file'  => 'modules/Qualiteam/SkinActProMembership/css/less/opc_paid_membership.less',
            'media' => 'screen',
            'merge' => 'bootstrap/css/bootstrap.less',
        ];
        return $list;
    }

    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/Qualiteam/SkinActProMembership/js/opc_paid_membership.js';
        return $list;
    }
}