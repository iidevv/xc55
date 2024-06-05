<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Geolocation\Module\XC\Onboarding\Controller\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * OnboardingWizard
 *
 * @Extender\Mixin
 * @Extender\Depend("XC\Onboarding")
 */
class OnboardingWizard extends \XC\Onboarding\Controller\Admin\OnboardingWizard
{
    public function doActionUpdateLocation()
    {
        parent::doActionUpdateLocation();

        \XLite\Core\Database::getRepo('\XLite\Model\Config')->createOption([
            'category' => 'XC\Onboarding',
            'name'     => 'disable_geolocation',
            'value'    => true,
        ]);
    }
}
