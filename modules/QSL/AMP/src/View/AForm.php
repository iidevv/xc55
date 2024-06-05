<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AMP\View;

use XCart\Extender\Mapping\Extender;
use XLite\Core\URLManager;

/**
 * @Extender\Mixin
 */
abstract class AForm extends \XLite\View\Form\AForm
{
    /**
     * Return list of additional params
     *
     * @return array
     */
    protected function getFormAttributes()
    {
        $attrs = parent::getFormAttributes();

        if (static::isAMP()) {
            // Use XHR based AMP forms when POST method is used
            if (strtolower($attrs['method']) === 'post') {
                $attrs['action-xhr'] = $attrs['action'];

                unset($attrs['action']);
            }

            // onsubmit is prohibited
            unset($attrs['onsubmit']);

            // Target is required for AMP forms
            if (!isset($attrs['target'])) {
                $attrs['target'] = '_top';
            }
        }

        return $attrs;
    }

    /**
     * Return value for the <form action="..." ...> attribute
     *
     * @return string
     */
    protected function getFormAction()
    {
        return static::isAMP() ? URLManager::getShopURL('?', null, [], null, null, true) : parent::getFormAction();
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return static::isAMP() ? 'modules/QSL/AMP/form/start.twig' : parent::getDefaultTemplate();
    }

    /**
     * Amp components
     *
     * @return array
     */
    protected function getAmpComponents()
    {
        return ['amp-form'];
    }
}
