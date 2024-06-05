<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OAuth2Client\View\Login;

/**
 * Abstract login widget
 */
abstract class ALogin extends \XLite\View\AView
{
    /**
     * Widget parameter names
     */
    public const PARAM_PROVIDER     = 'provider';
    public const PARAM_PLACEMENT    = 'placement';

    /**
     * @inheritdoc
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_PROVIDER     => new \XLite\Model\WidgetParam\TypeObject('Provider', null, false, 'QSL\OAuth2Client\Model\Provider'),
            static::PARAM_PLACEMENT    => new \XLite\Model\WidgetParam\TypeString('Placement', null),
        ];
    }

    /**
     * @inheritdoc
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getProvider();
    }

    /**
     * Get provider
     *
     * @return \QSL\OAuth2Client\Model\Provider
     */
    protected function getProvider()
    {
        return $this->getParam(static::PARAM_PROVIDER);
    }

    /**
     * Get placement
     *
     * @return string
     */
    protected function getPlacement()
    {
        return $this->getParam(static::PARAM_PLACEMENT);
    }

    /**
     * Auth action url which return service external link
     *
     * @return string
     */
    protected function getURL()
    {
        return $this->buildURL('oauth2return', 'auth', ['provider' => $this->getProvider()->getServiceName()]);
    }
}
