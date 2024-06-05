<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OAuth2Client\View\Form\ItemsList\Provider;

/**
 * Providers list table form
 */
class Table extends \XLite\View\Form\ItemsList\AItemsList
{
    /**
     * @inheritdoc
     */
    protected function getDefaultTarget()
    {
        return 'oauth2_client_providers';
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultAction()
    {
        return 'update';
    }
}
