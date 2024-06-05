<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\View\Model;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class Settings extends \XLite\View\Model\Settings
{
    /**
     * Get CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        if ($this->isPaypalSettings()) {
            $list[] = 'modules/CDev/Paypal/settings/style.css';
        }

        return $list;
    }

    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        if ($this->isPaypalSettings()) {
            $list[] = 'modules/CDev/Paypal/settings/module.js';
        }

        return $list;
    }

    /**
     * Check if current page is page with paypal settings
     *
     * @return boolean
     */
    protected function isPaypalSettings()
    {
        return $this->getTarget() === 'module'
            && $this->getModule()
            && $this->getModule() === 'CDev-Paypal';
    }

    /**
     * Get schema fields
     *
     * @return array
     */
    public function getSchemaFieldsForSection($section)
    {
        $list = parent::getSchemaFieldsForSection($section);

        if ($this->isPaypalSettings()) {
            foreach ($list as $name => $option) {
                if ($name == 'show_admin_welcome') {
                    unset($list[$name]);
                }
            }
        }

        return $list;
    }
}
