<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\RemoveData\Step;

use XCart\Extender\Mapping\Extender;

/**
 * Abstract step
 * @Extender\Mixin
 */
abstract class AStep extends \XLite\Logic\RemoveData\Step\AStep
{
    // {{{ Registry <editor-fold desc="Registry" defaultstate="collapsed">

    /**
     * Clean registry
     *
     * @return void
     */
    protected function cleanRegistry()
    {
        static $processed = null;

        if ($processed === null) {
            $registry = \XLite\Core\Database::getRepo('XC\MigrationWizard\Model\MigrationRegistry');
            $entry = $registry->findOneBy(['name' => $this->getRepository()->getClassName()]);

            if ($entry) {
                $registry->delete($entry, false);
            }

            $processed = true;
        }
    }

    // }}} </editor-fold>

    // {{{ Row processing <editor-fold desc="Row processing" defaultstate="collapsed">

    /**
     * Process model
     *
     * @param \XLite\Model\AEntity $model Model
     *
     * @return void
     */
    protected function processModel(\XLite\Model\AEntity $model)
    {
        parent::processModel($model);

        $this->cleanRegistry();
    }

    // }}} </editor-fold>
}
