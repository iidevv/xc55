<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductQuestions\View\Model;

use XCart\Extender\Mapping\Extender;

/**
 * Settings dialog model widget
 * @Extender\Mixin
 */
class Settings extends \XLite\View\Model\Settings
{
    /**
     * Get form field by option
     *
     * @param \XLite\Model\Config $option Option
     *
     * @return array
     */
    protected function getFormFieldByOption(\XLite\Model\Config $option)
    {
        $cell = parent::getFormFieldByOption($option);

        if (
            $cell
            && $option->getName() === 'product_questions_admin_email'
            && \Includes\Utils\Module\Manager::getRegistry()->isModuleEnabled('XC', 'MultiVendor')
        ) {
            $moduleUrl = \Includes\Utils\Module\Manager::getRegistry()->getModuleServiceURL('XC', 'MultiVendor');

            $cell[static::SCHEMA_COMMENT] = static::t(
                'This option is ignored as Multi-vendor module is installed. Sending all product questions to vendors email addresses.',
                [ 'url' => $moduleUrl ]
            );
        }

        return $cell;
    }
}
