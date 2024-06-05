<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Reviews\View\Model;

use XCart\Extender\Mapping\Extender;

/**
 * Module settings
 * @Extender\Mixin
 */
class ModuleSettings extends \XLite\View\Model\ModuleSettings
{
    /**
     * Runtime cache
     *
     * @var boolean
     */
    protected $isReviewModule;

    /**
     * Return true if current module is XC\Reviews
     */
    protected function isReviewModule()
    {
        if (!isset($this->isReviewModule)) {
            $this->isReviewModule = $this->getModule() && $this->getModule() === 'XC-Reviews';
        }

        return $this->isReviewModule;
    }

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

        if ($this->isReviewModule() && $option->getName() == 'followupTimeout') {
            $cell[static::SCHEMA_DEPENDENCY] = [
                static::DEPENDENCY_SHOW => [
                    'enableCustomersFollowup' => [true],
                ],
            ];
        }

        return $cell;
    }
}
