<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\RemoveData;

use XCart\Extender\Mapping\Extender;

/**
 * Generator
 * @Extender\Mixin
 */
class Generator extends \XLite\Logic\RemoveData\Generator
{
    /**
     * Return remove data generator steps list
     *
     * @return array
     */
    protected function getStepsList()
    {
        return array_merge(
            parent::getStepsList(),
            [
                'XC\MigrationWizard\Logic\RemoveData\Step\Memberships',
                'XC\MigrationWizard\Logic\RemoveData\Step\Payments',
                'XC\MigrationWizard\Logic\RemoveData\Step\Shippings',
                'XC\MigrationWizard\Logic\RemoveData\Step\Zones',
            ]
        );
    }

    //public function getStepsCount()
    //{
    //    $steps = [];
    //
    //    $list = $this->getStepsList();
    //    if (is_array($list)) {
    //        foreach ($list as $item) {
    //            $_step1  = explode('\\', $item);
    //            $_step2  = array_pop($_step1);
    //            $_step   = strtolower($_step2);
    //            $steps[] = $_step;
    //        }
    //    }
    //
    //    $this->setOptions(['steps' => $steps]);
    //
    //    $count = $this->count();
    //
    //    $this->setOptions([]);
    //
    //    return $count;
    //}
}
