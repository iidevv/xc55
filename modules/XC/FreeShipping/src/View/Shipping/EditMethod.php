<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FreeShipping\View\Shipping;

use XCart\Extender\Mapping\Extender;

/**
 * Edit shipping method dialog widget
 * @Extender\Mixin
 */
class EditMethod extends \XLite\View\Shipping\EditMethod
{
    /**
     * Shipping method
     *
     * @var \XLite\Model\Shipping\Method
     */
    protected $method;

    /**
     * Offline help template
     *
     * @return string
     */
    protected function getOfflineHelpTemplate()
    {
        /** @var \XC\FreeShipping\Model\Shipping\Method $method */
        $method = $this->getMethod();

        return $method && ($method->getFree() || $method->isFixedFee())
            ? 'modules/XC/FreeShipping/shipping/add_method/parts/offline_help.twig'
            : parent::getOfflineHelpTemplate();
    }

    /**
     * Returns help text
     *
     * @return string
     */
    protected function getHelpText()
    {
        $method = $this->getMethod();

        return $method->isFixedFee()
            ? static::t('Shipping freight tooltip text')
            : static::t('Free shipping tooltip text');
    }

    /**
     * Returns shipping method
     *
     * @return \XLite\Model\Shipping\Method
     */
    protected function getMethod()
    {
        if ($this->method === null) {
            $this->method = \XLite\Core\Database::getRepo('XLite\Model\Shipping\Method')->find(
                \XLite\Core\Request::getInstance()->methodId
            );
        }

        return $this->method;
    }
}
