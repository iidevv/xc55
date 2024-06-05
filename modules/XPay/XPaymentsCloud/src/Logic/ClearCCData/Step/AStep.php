<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\Logic\ClearCCData\Step;

use XLite\Logic\ARepoStep;
use XLite\Model\AEntity;

/**
 * Abstract step
 */
abstract class AStep extends ARepoStep
{
    /**
     * @inheritdoc
     */
    public function count()
    {
        if (!isset($this->countCache)) {
            $this->countCache = $this->getRepository()->countForClearCCData();
        }

        return $this->countCache;
    }

    /**
     * @inheritdoc
     */
    protected function processModel(AEntity $model)
    {
        $model->clearCCData();
    }

    /**
     * @inheritdoc
     */
    protected function getItems($reset = false)
    {
        if (!isset($this->items) || $reset) {
            $this->items = $this->getRepository()->getClearCCDataIterator($this->position);
            $this->items->rewind();
        }

        return $this->items;
    }

}
