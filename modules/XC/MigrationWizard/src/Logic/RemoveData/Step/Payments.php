<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\RemoveData\Step;

/**
 * Step
 */
class Payments extends \XLite\Logic\RemoveData\Step\AStep
{
    // {{{ Properties <editor-fold desc="Properties" defaultstate="collapsed">

    protected $processorsToDisable = [
        'XLite\Model\Payment\Processor\COD',
        'XLite\Model\Payment\Processor\Check',
        'XLite\Model\Payment\Processor\PhoneOrdering',
        'XLite\Model\Payment\Processor\PurchaseOrder',
    ];

    protected $processorsToRemove = [
        'XLite\Model\Payment\Processor\Offline',
    ];

    // }}} </editor-fold>

    // {{{ Data <editor-fold desc="Data" defaultstate="collapsed">

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Payment\Method');
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
        if (
            in_array($model->class, $this->processorsToRemove)
        ) {
            parent::processModel($model);
            return;
        }

        if (
            in_array($model->class, $this->processorsToDisable)
        ) {
            $this->getRepository()->update($model, ['enabled' => false], false);
            return;
        }

        if ($model->added) {
            $this->getRepository()->update($model, ['added' => false], false);
        }
    }

    // }}} </editor-fold>
}
