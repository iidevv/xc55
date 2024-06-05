<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsConnector\View\Form;

/**
 * Deploy configuration form
 *
 */
class DeployConfiguration extends \Qualiteam\SkinActXPaymentsConnector\View\Form\Settings
{
    /**
     * Get default action
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'deploy_configuration';
    }
}
