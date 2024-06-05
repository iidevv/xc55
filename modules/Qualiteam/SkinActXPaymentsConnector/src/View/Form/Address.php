<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsConnector\View\Form;

use XCart\Extender\Mapping\Extender;
use XLite\Controller\AController;
use XLite\Core\Request;

/**
 * Profile abstract form
 *
 * @Extender\Mixin
 */
class Address extends \XLite\View\Form\Address\Address
{
    /**
     * getDefaultParams
     *
     * @return array
     */
    protected function getDefaultParams()
    {
        $result = parent::getDefaultParams();

        if (Request::getInstance()->zero_auth) {
            $result[AController::RETURN_URL] = $this->buildURL('add_new_card');
        }

        return $result;
    }
}
