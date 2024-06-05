<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActProMembership\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Product extends \XLite\Model\Repo\Product
{

    public function getProMembershipProductsCount()
    {
        return count($this->getProMembershipProducts());
    }

    public function getProMembershipProducts()
    {
        return $this->prepareProMembershipProducts()->getResult();
    }

    protected function prepareProMembershipProducts()
    {
        return $this->createQueryBuilder()
            ->andWhere('p.enabled = :enabledProMembershipProduct')
            ->andWhere('p.appointmentMembership IS NOT NULL')
            ->setParameter('enabledProMembershipProduct', true);
    }
}