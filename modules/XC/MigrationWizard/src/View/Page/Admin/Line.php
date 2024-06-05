<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\View\Page\Admin;

use XCart\Extender\Mapping\ListChild;

/**
 * Wizard dialog
 *
 * @ListChild (list="migration_wizard.sections", zone="admin", weight="100")
 */
class Line extends \XLite\View\AView
{
    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return self::MIGRATION_WIZARD_MODULE_PATH . '/steps/line.twig';
    }

    /**
     * @return array
     */
    protected function getSteps()
    {
        $list = [
            'XC\MigrationWizard\Logic\Migration\Step\Start',
            'XC\MigrationWizard\Logic\Migration\Step\Connect',
            'XC\MigrationWizard\Logic\Migration\Step\CheckRequirements',
            'XC\MigrationWizard\Logic\Migration\Step\DetectTransferableData',
        ];

        $lastStep = \XC\MigrationWizard\Logic\Migration\Wizard::getInstance()->getLastStep();

        if (get_class($lastStep) === 'XC\MigrationWizard\Logic\Migration\Step\MissingModules') {
            $list[] = 'XC\MigrationWizard\Logic\Migration\Step\MissingModules';
        }

        return array_merge(
            $list,
            [
                'XC\MigrationWizard\Logic\Migration\Step\TransferData',
                'XC\MigrationWizard\Logic\Migration\Step\Complete',
            ]
        );
    }

    /**
     * @param string $step
     *
     * @return bool
     */
    protected function isEnabledStep($step)
    {
        $lastStep = \XC\MigrationWizard\Logic\Migration\Wizard::getInstance()->getLastStep();

        return get_class($lastStep) === $step;
    }

    /**
     * @param string $step
     *
     * @return bool
     */
    protected function hasLeftArrow($step)
    {
        $steps = $this->getSteps();

        return $step !== reset($steps);
    }

    /**
     * @param string $step
     *
     * @return bool
     */
    protected function hasRightArrow($step)
    {
        $steps = $this->getSteps();

        return $step !== end($steps);
    }

    /**
     * @param \XC\MigrationWizard\Logic\Migration\Step\AStep $step
     *
     * @return string
     */
    protected function getLineTitle($step)
    {
        return static::t($step::getLineTitle());
    }
}
