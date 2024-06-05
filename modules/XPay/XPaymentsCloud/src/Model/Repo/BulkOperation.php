<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\Model\Repo;

/**
 * Bulk operation repository
 */
class BulkOperation extends \XLite\Model\Repo\ARepo
{
    /**
     * Get field name with allias
     *
     * @param string $field
     *
     * @return string
     */
    protected function getAlliasedField(string $field)
    {
        return sprintf('%s.%s', $this->getDefaultAlias(), $field);
    }

    /**
     * Get active batch ID for operation 
     *
     * @param string $operation
     *
     * @return integer
     */
    public function getActiveBatchId(string $operation)
    {
        $result = $this->getOperationQueryBuilder($operation)
            ->andWhere($this->getAlliasedField('status') . ' = :status')
            ->setParameter('operation', $operation)
            ->setParameter('status', \XPay\XPaymentsCloud\Model\BulkOperation::STATUS_IN_PROGRESS)
            ->select($this->getAlliasedField('batchId'))
            ->getSingleScalarResult();

        return $result;
    }

    /**
     * Get operation query builder 
     *
     * @param string $operation
     *
     * @return \Doctrine\ORM\QueryBuilder 
     */
    protected function getOperationQueryBuilder()
    {
        return $this->createPureQueryBuilder($this->getDefaultAlias())
            ->andWhere($this->getDefaultAlias() . '.operation = :operation');
    }
}
