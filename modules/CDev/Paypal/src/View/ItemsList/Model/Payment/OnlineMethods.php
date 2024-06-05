<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\View\ItemsList\Model\Payment;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class OnlineMethods extends \XLite\View\ItemsList\Model\Payment\OnlineMethods
{
    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        $result->{\XLite\Model\Repo\Payment\Method::P_EXCLUDED_SERVICE_NAMES} = [\CDev\Paypal\Main::PP_METHOD_PC];

        return $result;
    }
}
