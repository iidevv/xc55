<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsConnector\Controller\Customer;

/**
 * X-Payment connector iframe 
 *
 */
class XpcIframe extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Get viewer template
     *
     * @return string 
     */
    protected function getViewerTemplate()
    {
        return 'modules/Qualiteam/SkinActXPaymentsConnector/checkout/iframe/main.twig';
    }

}
