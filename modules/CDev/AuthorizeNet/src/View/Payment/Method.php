<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\AuthorizeNet\View\Payment;

use XCart\Extender\Mapping\Extender;
use CDev\AuthorizeNet\Model\Payment\Processor\AuthorizeNetSIM;

/**
 * @Extender\Mixin
 */
class Method extends \XLite\View\Payment\Method
{
    /**
     * @inheritdoc
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        if ($this->isAuthorizeNetSIM()) {
            $list[] = 'modules/CDev/AuthorizeNet/script.js';
        }

        return $list;
    }

    /**
     * @return bool
     */
    protected function isAuthorizeNetSIM()
    {
        return $this->getPaymentMethod()
            && $this->getPaymentMethod()->getProcessor()
            && $this->getPaymentMethod()->getProcessor() instanceof AuthorizeNetSIM;
    }
}
