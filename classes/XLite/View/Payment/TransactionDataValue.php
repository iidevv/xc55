<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Payment;

/**
 * Order items summary
 */
abstract class TransactionDataValue extends \XLite\View\AView
{
    /**
     *  Widget parameters names
     */
    public const PARAM_CELL    = 'cell';

    /**
     * TransactionData (cache)
     *
     * @var \XLite\Model\Payment\TransactionData
     */
    protected $cell = null;

    /**
     * Get TransactionData cell
     *
     * @return \XLite\Model\Payment\TransactionData
     */
    public function getCell()
    {
        if ($this->cell === null) {
            if ($this->getParam(self::PARAM_CELL) instanceof \XLite\Model\Payment\TransactionData) {
                $this->cell = $this->getParam(self::PARAM_CELL);
            }
        }

        return $this->cell;
    }

    /**
     * Returns either
     *
     * @return mixed
     */
    protected function getValue()
    {
        return $this->getCell()->getValue();
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_CELL    => new \XLite\Model\WidgetParam\TypeObject('Transaction data cell', null, false, '\XLite\Model\Payment\TransactionData'),
        ];
    }

    /**
     * Check widget visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->getCell();
    }
}
