<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Migration\Step;

/**
 * Abstract Step Logic
 */
abstract class AStep implements \XC\MigrationWizard\Logic\Migration\Step\IStep
{
    /**
     * View classes
     *
     * @var array
     */
    protected $views = [];

    /**
     * Action classes
     *
     * @var array
     */
    protected $actions = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        $class = $this->getStepName();

        $this->views = [
            "XC\\MigrationWizard\\View\\Migration\\Step\\{$class}" //TODO checkaim
        ];

        $this->actions = [
            "XC\\MigrationWizard\\View\\Migration\\Action\\{$class}"
        ];
    }

    /**
     * Checks current step
     *
     * @return boolean
     */
    public function hasView($object)
    {
        return in_array(get_class($object), $this->views);
    }

    /**
     * Check action
     *
     * @return boolean
     */
    public function hasAction($object)
    {
        return in_array(get_class($object), $this->actions);
    }

    /**
     * Get step name
     *
     * @return string
     */
    public function getStepName()
    {
        return (new \ReflectionObject($this))->getShortName();
    }

    /**
     * Get connection
     *
     * @return \PDO|bool
     */
    public function getConnection()
    {
        if (\XLite::getController()->getWizard()->getStep('Connect')) {
            return \XLite::getController()->getWizard()->getStep('Connect')->getConnection();
        }

        return false;
    }

    /**
     * Get table prefix
     *
     * @return string or false
     */
    public function getTablePrefix()
    {
        if (\XLite::getController()->getWizard()->getStep('Connect')) {
            return \XLite::getController()->getWizard()->getStep('Connect')->getPrefix();
        }

        return false;
    }
}
