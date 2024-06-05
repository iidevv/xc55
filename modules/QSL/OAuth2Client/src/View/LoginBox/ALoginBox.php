<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OAuth2Client\View\LoginBox;

/**
 * Abstract login sign-in widget
 */
abstract class ALoginBox extends \XLite\View\AView
{
    /**
     * Cached login widgets
     *
     * @var \QSL\OAuth2Client\View\Login\ALogin[]
     */
    protected $cached_providers;

    /**
     * Get placement
     *
     * @return string
     */
    abstract protected function getPlacement();

    /**
     * Get all configured authentication providers
     *
     * @return \QSL\OAuth2Client\View\Login\ALogin[]
     */
    public function getProviderWidgets()
    {
        if (!isset($this->cached_providers)) {
            $this->cached_providers = [];
            /** @var \QSL\OAuth2Client\Model\Provider $provider */ #nolint
            foreach (\XLite\Core\Database::getRepo('\QSL\OAuth2Client\Model\Provider')->findActive() as $provider) {
                $wrapper = $provider->getWrapper();
                if ($provider->isWidgetVisible($this->getPlacement())) {
                    $parameters = $wrapper->getWidgetParameters()
                        + [
                            \QSL\OAuth2Client\View\Login\ALogin::PARAM_PLACEMENT => $this->getPlacement(),
                        ];

                    $this->cached_providers[] = $this->getWidget($parameters, $wrapper->getWidgetClass());
                }
            }
        }

        return $this->cached_providers;
    }

    /**
     * @inheritdoc
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && !\XLite\Core\Auth::getInstance()->isLogged()
            && count($this->getProviderWidgets()) > 0;
    }

    /**
     * Returns widget style class
     *
     * @return string
     */
    protected function getStyleClass()
    {
        return 'oauth2-login-container oauth2-login-' . $this->getPlacement();
    }
}
