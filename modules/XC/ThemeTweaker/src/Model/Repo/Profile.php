<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\Model\Repo;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\NoResultException;

/**
 * @Extender\Mixin
 */
class Profile extends \XLite\Model\Repo\Profile
{
    /**
     * @return \XLite\Model\Profile|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findDumpProfile()
    {
        $qb = $this->createPureQueryBuilder('p');
        $expr = $qb->expr();
        $qb->where($qb->expr()->andX(
            $expr->isNull('p.order'),
            $expr->gte('p.access_level', \XLite\Core\Auth::getInstance()->getAdminAccessLevel())
        ))
            ->setMaxResults(1)
            ->orderBy('p.added');


        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            $qb->where($qb->expr()->andX(
                $expr->isNull('p.order')
            ));

            try {
                return $qb->getQuery()->getSingleResult();
            } catch (NoResultException $e) {
                return null;
            }
        }
    }
}
