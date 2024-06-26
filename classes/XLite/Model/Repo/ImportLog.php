<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

/**
 * Import log repository
 */
class ImportLog extends \XLite\Model\Repo\ARepo
{
    /**
     * Return count of error/warning logs
     *
     * @return integer
     */
    public function countLogs()
    {
        return $this->defineCountQuery()
            ->andWhere('i.type IN (:types)')
            ->setParameter('types', [\XLite\Model\ImportLog::TYPE_WARNING, \XLite\Model\ImportLog::TYPE_ERROR])
            ->count();
    }

    /**
     * Return files list
     *
     * @return array
     */
    public function findFiles()
    {
        return $this->createQueryBuilder('il')
            ->select('il.file')
            ->addSelect('SUM(IFELSE(il.type = \'W\', 1, 0)) countW')
            ->addSelect('SUM(IFELSE(il.type = \'E\', 1, 0)) countE')
            ->addSelect('il.processor')
            ->groupBy('il.file')
            ->getResult();
    }

    /**
     * Return errors list
     *
     * @param string $file File
     *
     * @return array
     */
    public function findErrorsByFile($file)
    {
        return $this->createQueryBuilder('il')
            ->andWhere('il.file = :file')
            ->setParameter('file', $file)
            ->andWhere('il.type IN (:types)')
            ->setParameter('types', [\XLite\Model\ImportLog::TYPE_WARNING, \XLite\Model\ImportLog::TYPE_ERROR])
            ->orderBy('il.type')
            ->addOrderBy('il.file')
            ->addOrderBy('il.row')
            ->getArrayResult();
    }

    /**
     *  Query for searching by value in arguments
     *
     * @param string $type Log entry type
     * @param string $code Log entry code
     * @param string $value Log entry arguments.value
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function prepareQueryBuilderForFindByValue($type, $code, $value)
    {
        return $this->createQueryBuilder('il')
            ->andWhere('il.type = :type')
            ->andWhere('il.code = :code')
            ->andWhere('il.arguments LIKE :arguments')
            ->setParameter('arguments', '%"' . $value . '"%')
            ->setParameter('type', $type)
            ->setParameter('code', $code);
    }

    /**
     *  Find log entries by value in arguments
     *
     * @param string $type Log entry type
     * @param string $code Log entry code
     * @param string $value Log entry arguments.value
     *
     * @return array
     */
    public function findByValue($type, $code, $value)
    {
        $qb = $this->prepareQueryBuilderForFindByValue($type, $code, $value);
        return $qb->getResult();
    }

    /**
     * Count log entries by value in arguments
     * @param string $type Log entry type
     * @param string $code Log entry code
     * @param string $value Log entry arguments.value
     *
     * @return integer
     */
    public function countByValue($type, $code, $value)
    {
        $qb = $this->prepareQueryBuilderForFindByValue($type, $code, $value);
        $qb->select('COUNT(il)');

        return intval($qb->getSingleScalarResult());
    }

    /**
     * Delete log records by type
     *
     * @param string $type Log entry type (e.g. W or E)
     *
     * @return void
     */
    public function deleteByType($type)
    {
        $this->prepareDeleteByTypeQueryBuilder($type)->execute();
    }

    /**
     * Prepare query builder for deleteByType() method
     *
     * @param string $type Log entry type
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function prepareDeleteByTypeQueryBuilder($type)
    {
        return $this->getQueryBuilder()
            ->delete($this->_entityName, 'il')
            ->andWhere('il.type = :type')
            ->setParameter('type', $type);
    }

    /**
     * Check sku existing while current import session
     *
     * @param string $sku
     *
     * @return boolean
     */
    public function isSkuWillBeAdded(string $sku)
    {
        return \XLite\Core\Cache\ExecuteCached::executeCachedRuntime(function () use ($sku) {
            foreach ($this->getAddedSku() as $list) {
                if (in_array($sku, $list->getArguments())) {
                    return true;
                }
            }

            return false;
        }, [__CLASS__, __METHOD__, $sku]);
    }

    /**
     * @return ImportLog[]
     */
    public function getAddedSku()
    {
        return \XLite\Core\Cache\ExecuteCached::executeCachedRuntime(function () {
            return $this->findByType(\XLite\Model\ImportLog::TYPE_SKU);
        }, [__CLASS__, __METHOD__]);
    }
}
