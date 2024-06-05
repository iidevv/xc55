<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OAuth2Client\Model\Repo;

/**
 * Providers repository
 */
class Provider extends \XLite\Model\Repo\ARepo
{
    /**
     * @inheritdoc
     */
    protected $alternativeIdentifier = [
        ['service_name'],
    ];

    /**
     * Get active providers
     *
     * @return \QSL\OAuth2Client\Model\Provider[]
     */
    public function findActive()
    {
        return $this->defineQueryBuilderActive()->getResult();
    }

    /**
     * Count active providers
     *
     * @return integer
     */
    public function countActive()
    {
        return (int)$this->defineQueryBuilderCountActive()->getSingleScalarResult();
    }

    /**
     * Get query builder for findActive() method
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineQueryBuilderActive()
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.enabled = :enabled')
            ->setParameter('enabled', true)
            ->orderBy('p.position');
    }

    /**
     * Get query builder for countActive() method
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineQueryBuilderCountActive()
    {
        return $this->defineQueryBuilderActive()
            ->selectCount();
    }
}
