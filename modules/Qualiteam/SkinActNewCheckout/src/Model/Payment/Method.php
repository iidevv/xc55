<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActNewCheckout\Model\Payment;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class Method extends \XLite\Model\Payment\Method
{
    /**
        For example, you could fill in .env.local:
    XP_ALWAYS_ENABLED=1

    XP_SETTING_ACCOUNT=xc55devspaandequipmentcom
    XP_SETTING_API_KEY=ht7i3xk2F6chQVIKpRQ8xOGcsLReJUCI
    XP_SETTING_QUICKACCESS_KEY=Xu3iTY29KiWQj6kSnuuKZUYJqCeHVcm2
    XP_SETTING_SECRET_KEY=r1NRsvdoT7dnqFoxlRKbqerii6HqFaas
    XP_SETTING_WIDGET_KEY=SS1eW9dnCwy7JzxSWPIrYP3gKfI4HvIH

     * @param $name
     *
     * @return mixed|string|void|null
     */
    public function getSetting($name)
    {
        if ($this->getServiceName() === 'XPaymentsCloud') {
            return $this->getXPSetting($name);
        }

        return parent::getSetting($name);
    }

    protected function getXPSetting($name)
    {
        $key = 'XP_SETTING_' . strtoupper($name);

        return $_ENV[$key] ?? parent::getSetting($name);
    }
}
