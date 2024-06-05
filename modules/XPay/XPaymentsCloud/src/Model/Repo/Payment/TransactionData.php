<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\Model\Repo\Payment;

use XCart\Extender\Mapping\Extender;
use XPay\XPaymentsCloud\Core\EventListener\ClearCCData;

/**
 * Transaction data storage
 *
 * @Extender\Mixin
 */
abstract class TransactionData extends \XLite\Model\Repo\Payment\TransactionData implements \XLite\Base\IDecorator
{
    /**
     * Count items for clear credit cards data
     *
     * @return integer
     */
    public function countForClearCCData()
    {
        $result = (int)$this->defineCountForClearCCDataQuery()->getSingleScalarResult();

        return $result;
    }

    /**
     * Define query builder for COUNT query
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineCountForClearCCDataQuery()
    {
        return $this->getClearCCDataQueryBuilder()->selectCount();
    }

    /**
     * Define items iterator
     *
     * @param integer $position Position OPTIONAL
     *
     * @return \Iterator
     */
    public function getClearCCDataIterator($position = 0)
    {
        return $this->defineClearCCDataIteratorQueryBuilder($position)->iterate();
    }

    /**
     * Define quick data iterator query builder
     *
     * @param integer $position Position
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineClearCCDataIteratorQueryBuilder($position)
    {
        return $this->getClearCCDataQueryBuilder()
            ->setFirstResult($position)
            ->setMaxResults(ClearCCData::CHUNK_LENGTH);
    }

    /**
     * @inheritdoc
     */
    protected function getClearCCDataQueryBuilder()
    {
        return $this->createPureQueryBuilder($this->getDefaultAlias())
            ->andWhere($this->getDefaultAlias() . '.name = :card_number')
            ->orWhere($this->getDefaultAlias() . '.name = :exp_date')
            ->setParameter('card_number', 'xpaymentsCardNumber')
            ->setParameter('exp_date', 'xpaymentsCardExpirationDate');
    }
}
