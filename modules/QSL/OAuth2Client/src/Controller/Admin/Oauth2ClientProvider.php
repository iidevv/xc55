<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OAuth2Client\Controller\Admin;

/**
 * Provider controller
 */
class Oauth2ClientProvider extends \XLite\Controller\Admin\AAdmin
{
    /**
     * @inheritdoc
     */
    protected $params = ['target', 'id'];

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return $this->getModelForm()->getModelObject()
            ? $this->getModelForm()->getModelObject()->getName()
            : static::t('Provider');
    }

    /**
     * @inheritdoc
     */
    public function checkAccess()
    {
        return parent::checkAccess()
            && $this->getModelForm()->getModelObject();
    }

    /**
     * Get provider
     *
     * @return \QSL\OAuth2Client\Model\Provider
     */
    public function getProvider()
    {
        return $this->getModelForm()->getModelObject();
    }

    /**
     * Update model
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        if ($this->getModelForm()->performAction('modify')) {
            $this->setReturnURL(\XLite\Core\Converter::buildURL('oauth2_client_providers'));
        }
    }

    /**
     * @inheritdoc
     */
    protected function getModelFormClass()
    {
        return 'QSL\OAuth2Client\View\Model\Provider';
    }
}
