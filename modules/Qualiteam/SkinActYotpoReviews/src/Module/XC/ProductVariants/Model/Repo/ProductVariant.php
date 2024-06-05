<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Module\XC\ProductVariants\Model\Repo;

use Doctrine\ORM\QueryBuilder;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Config;

/**
 * @Extender\Mixin
 */
class ProductVariant extends \XC\ProductVariants\Model\Repo\ProductVariant
{
    public function findVariantsToCreateYotpoId()
    {
        $qb = $this->createQueryBuilder('pv')
            ->andWhere('pv.isYotpoSync = :isYotpoSync')
            ->andWhere('pv.yotpo_id IS NULL')
            ->setParameter('isYotpoSync', false);

        if ($this->isDevMode()) {
            $this->getDevPart($qb);
        }

        return $qb
            ->setMaxResults(100)
            ->getQuery()->getResult();
    }

    protected function isDevMode()
    {
        return Config::getInstance()->Qualiteam->SkinActYotpoReviews->yotpo_dev_mode;
    }

    protected function getDevPart(QueryBuilder $queryBuilder)
    {
        $queryBuilder
            ->linkLeft('pv.product', 'p')
            ->linkLeft('p.translations', 'pt')
            ->andWhere('pt.name LIKE :devModelProductPrefix')
            ->setParameter('devModelProductPrefix', '%' . $this->getDevModeProductPrefix() . '%');
    }

    protected function getDevModeProductPrefix()
    {
        return Config::getInstance()->Qualiteam->SkinActYotpoReviews->yotpo_product_prefix;
    }

    public function findVariantsToUpdateYotpoId()
    {
        $qb = $this->createQueryBuilder('pv')
            ->andWhere('pv.isYotpoSync = :isYotpoSync')
            ->andWhere('pv.yotpo_id IS NOT NULL')
            ->setParameter('isYotpoSync', false);

        if ($this->isDevMode()) {
            $this->getDevPart($qb);
        }

        return $qb
            ->setMaxResults(100)
            ->getQuery()->getResult();
    }
}