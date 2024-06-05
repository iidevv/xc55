<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\AvaTax\Module\XC\MultiVendor\View\FormField\Select;

use XCart\Extender\Mapping\Extender;

/**
 * Automatic tax calculate type selector
 *
 * @Extender\Mixin
 * @Extender\Rely("XC\MultiVendor")
 */
class AutomaticTaxCalculateType extends \XC\AvaTax\View\FormField\Select\AutomaticTaxCalculateType
{
    /**
     * @return bool
     */
    protected function isVendorsOwnTaxes()
    {
        return !\XC\MultiVendor\Main::isWarehouseMode()
            && \XLite\Core\Config::getInstance()->XC->MultiVendor->taxes_owner === \XC\MultiVendor\Model\Commission::TAXES_OWNER_VENDOR;
    }

    /**
     * @inheritdoc
     */
    public function __construct(array $params = [])
    {
        if ($this->isVendorsOwnTaxes() && \XLite::getController()->getTarget() === 'module') {
            $params[static::PARAM_HELP] = static::t('In the “Vendors as separate shops” mode, the store administrator can enable vendors to collect taxes and define the states to send the calc request for (on a per vendor basis) via the “Financial details” tab of the corresponding vendor profiles');
        }

        parent::__construct($params);
    }
}
