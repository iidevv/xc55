<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\UPS\Model\Shipping\Mapper\RateCOD;

use XLite\Core;
use XC\UPS\Model\Shipping\Mapper\Rate;

/**
 * Get quote output mapper
 */
class OutputMapper extends Rate\OutputMapper
{
    /**
     * @param \SimpleXMLElement $ratedShipment
     *
     * @return \XLite\Model\Shipping\Rate|null
     */
    protected function getRate($ratedShipment)
    {
        $result = parent::getRate($ratedShipment);

        if ($result) {
            $requestData = $this->getAdditionalData('request');
            $srcCountry = $requestData['srcAddress']['country'];
            $dstCountry = $requestData['dstAddress']['country'];
            if ($this->isAnyCODAllowed($srcCountry, $dstCountry)) {
                $extraData = $result->getExtraData() ?: new Core\CommonCell();

                $extraData->cod_supported = true;
                $extraData->cod_rate = $result->getBaseRate();

                $result->setExtraData($extraData);
            }
        }

        return $result;
    }
}
