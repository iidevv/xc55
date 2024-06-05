<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\USPS\Model;

use XCart\Extender\Mapping\Extender;
use CDev\USPS\Model\Shipping\Processor\PB;
use CDev\USPS\Model\Shipping\Processor\USPS;

/**
 * @Extender\Mixin
 */
class Shipping extends \XLite\Model\Shipping
{
    /**
     * @param string $processorId
     *
     * @return null|\XLite\Model\Shipping\Processor\AProcessor
     */
    public static function getProcessorObjectByProcessorId($processorId)
    {
        if ($processorId === 'usps') {
            $result = null;

            $processors = \XLite\Model\Shipping::getInstance()->getProcessors();
            $config = \XLite\Core\Config::getInstance()->CDev->USPS;
            foreach ($processors as $obj) {
                if (
                    ($config->dataProvider === 'pitneyBowes' && $obj instanceof PB)
                    || ($config->dataProvider !== 'pitneyBowes' && $obj instanceof USPS)
                ) {
                    $result = $obj;
                    break;
                }
            }

            return $result;
        } else {
            return parent::getProcessorObjectByProcessorId($processorId);
        }
    }
}
