<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Payment\Method\Admin;

/**
 * Alternative payment methods
 */
class NonPredefinedGateways extends \XLite\View\ItemsList\Payment\Method\Admin\AAdmin
{
    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $cnd = parent::getSearchCondition();

        $cnd->{\XLite\Model\Repo\Payment\Method::P_PREDEFINED} = false;
        $cnd->{\XLite\Model\Repo\Payment\Method::P_TYPE}       = [
            \XLite\Model\Payment\Method::TYPE_ALLINONE,
            \XLite\Model\Payment\Method::TYPE_CC_GATEWAY,
            \XLite\Model\Payment\Method::TYPE_ALTERNATIVE,
        ];

        return $cnd;
    }
}
