<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button;

/**
 * Activate license key popup button
 */
class ActivateKey extends \XLite\View\Button\APopupButton
{
    public const PARAM_IS_MODULE = 'isModule';

    public function getJSFiles(): array
    {
        $list   = parent::getJSFiles();
        $list[] = 'button/js/activate_key.js';

        return $list;
    }

    protected function getDefaultLabel(): string
    {
        return 'Activate your X-Cart';
    }

    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_IS_MODULE => new \XLite\Model\WidgetParam\TypeInt('Is module activation', 0),
        ];
    }

    protected function prepareURLParams(): array
    {
        $params = [
            'target'    => 'activate_key',
            'widget'    => '\XLite\View\LicenseManager\LicenseKey',
            'returnUrl' => \XLite\Core\URLManager::getCurrentURL(),
        ];

        if ($this->isModuleActivation()) {
            $params['isModule'] = true;
        }

        return $params;
    }

    protected function getClass(): string
    {
        return parent::getClass() . ' activate-key';
    }

    protected function isModuleActivation(): bool
    {
        return $this->getParam(static::PARAM_IS_MODULE);
    }
}
