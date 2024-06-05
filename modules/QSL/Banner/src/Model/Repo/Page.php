<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Banner\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("CDev\SimpleCMS")
 */
class Page extends \CDev\SimpleCMS\Model\Repo\Page
{
    /**
     * Find all enabled languages
     *
     * @return array
     */
    public function findActivePages()
    {
        return $this->defineActiveStaticPagesQuery()->getResult();
    }

    /**
     * Define query builder for findActivePages()
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineActiveStaticPagesQuery()
    {
        return $this->createQueryBuilder()
            ->where('p.enabled = :true')
            ->andWhere('p.type = :type')
            ->setParameter('true', true)
            ->setParameter('type', \CDev\SimpleCMS\Model\Page::TYPE_DEFAULT);
    }
}
