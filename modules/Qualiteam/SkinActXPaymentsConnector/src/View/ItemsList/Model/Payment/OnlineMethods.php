<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsConnector\View\ItemsList\Model\Payment;

use Qualiteam\SkinActXPaymentsConnector\Core\Settings;
use XCart\Extender\Mapping\Extender;
use XLite\Core\CommonCell;
use XLite\Model\Payment\Method;

/**
 * Decorate methods items list. We should exclude duplicated XP payment methods here
 *
 * @Extender\Mixin
 */
class OnlineMethods extends \XLite\View\ItemsList\Model\Payment\OnlineMethods
{
    /**
     * Return payment methods list with excluded XP duplicates
     *
     * @param CommonCell $cnd       Search condition
     * @param boolean                $countOnly Return items list or only its size OPTIONAL
     *
     * @return array|integer
     */
    protected function getData(CommonCell $cnd, $countOnly = false)
    {
        $data = parent::getData($cnd, false);

        $xpMethods = [];

        /** @var Method $pm */
        foreach ($data as $key => $pm) {
            if (Settings::XP_MODULE_NAME == $pm->getModuleName()) {
                if (!array_key_exists($pm->getServiceName(), $xpMethods)) {
                    $xpMethods[$pm->getServiceName()] = $pm;
                    $data[$key]->setName($pm->getOriginalName());
                } else {
                    unset($data[$key]);
                }
            }
        }

        return $countOnly
            ? count($data)
            : $data;
    }
}
