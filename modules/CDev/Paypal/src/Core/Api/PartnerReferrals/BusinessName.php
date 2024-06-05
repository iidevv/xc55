<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\Core\Api\PartnerReferrals;

use PayPal\Common\PayPalModel;

/**
 * https://developer.paypal.com/docs/api/partner-referrals/#definition-business_name
 *
 * @property string type
 * @property string name
 */
class BusinessName extends PayPalModel
{
    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Valid Values: ["LEGAL", "DOING_BUSINESS_AS", "STOCK_TRADING_NAME"]
     *
     * @param string $type
     *
     * @return BusinessName
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return BusinessName
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }
}
