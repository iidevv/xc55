<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

/**
 * Notifications repository
 */
class Notification extends \XLite\Model\Repo\Base\I18n
{
    /**
     * Alternative record identifiers
     *
     * @var array
     */
    protected $alternativeIdentifier = [
        ['templatesDirectory'],
    ];

    protected $defaultOrderBy = 'position';

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getModular()
    {
        return $this
            ->createOriginalQueryBuilder()
            ->where('n.module is NOT NULL')
            ->getResult();
    }

    public function disableNotificationsByModule(string $module): void
    {
        $this
            ->getQueryBuilder()
            ->update($this->_entityName, 'n')
            ->set('n.enabledForAdmin', 0)
            ->set('n.enabledForCustomer', 0)
            ->where('n.module = :module')
            ->setParameter('module', $module)
            ->execute();
    }

    /**
     * @param string $alias
     * @param string $indexBy
     * @param string $code
     *
     * @return \Doctrine\ORM\QueryBuilder|\XLite\Model\QueryBuilder\AQueryBuilder
     */
    public function createQueryBuilder($alias = null, $indexBy = null, $code = null)
    {
        $qb = parent::createQueryBuilder($alias, $indexBy, $code);

        return $qb->where('n.available = 1');
    }

    /**
     * @param string $alias
     * @param string $indexBy
     * @param string $code
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    public function createOriginalQueryBuilder($alias = null, $indexBy = null, $code = null)
    {
        return parent::createQueryBuilder($alias, $indexBy, $code);
    }
}
