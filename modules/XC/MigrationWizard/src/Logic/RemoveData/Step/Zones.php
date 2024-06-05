<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\RemoveData\Step;

/**
 * Step
 */
class Zones extends \XC\MigrationWizard\Logic\RemoveData\Step\AStep
{
    // {{{ Data <editor-fold desc="Data" defaultstate="collapsed">

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Zone');
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
        if (!$model->is_default) {
            parent::processModel($model);
        }
    }

    // }}} </editor-fold>
}
