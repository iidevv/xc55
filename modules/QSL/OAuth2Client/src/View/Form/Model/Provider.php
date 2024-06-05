<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OAuth2Client\View\Form\Model;

/**
 * Provider model form
 */
class Provider extends \XLite\View\Form\AForm
{
    /**
     * @inheritdoc
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/QSL/OAuth2Client/provider/style.css';

        return $list;
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultTarget()
    {
        return 'oauth2_client_provider';
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultAction()
    {
        return 'update';
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultClassName()
    {
        return trim(parent::getDefaultClassName() . ' validationEngine oauth-client-provider');
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultParams()
    {
        return [
            'id' => \XLite\Core\Request::getInstance()->id,
        ];
    }
}
