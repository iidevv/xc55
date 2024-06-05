<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\View\Form\Migration;

/**
 * Migration Wizard - Abstract Form
 */
abstract class AForm extends \XLite\View\Form\AForm
{
    /**
     * Get short class name
     *
     * @return string
     */
    protected function getObjectClassName()
    {
        return (new \ReflectionClass($this))->getShortName();
    }

    /**
     * Return default value for the "target" parameter
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'migration_wizard';
    }

    /**
     * Return list of the form default parameters
     *
     * @return array
     */
    protected function getDefaultParams()
    {
        return [
            'action' => $this->getObjectClassName(),
        ];
    }
}
