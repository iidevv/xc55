<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVerifiedCustomer\Model\Repo;

use Qualiteam\SkinActVerifiedCustomer\Model\VerificationInfo;
use XCart\Extender\Mapping\Extender;

/**
 * Order repository
 * @Extender\Mixin
 */
class Order extends \XLite\Model\Repo\Order
{
    /**
     * Allowable search params
     */
    public const SEARCH_VERIFICATION_STATUS = 'verificationStatus';

    /**
     * Prepare certain search condition
     *
     * @param \XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder Query builder to prepare
     * @param integer $value Condition data
     *
     * @return void
     */
    protected function prepareCndVerificationStatus(\XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder, $value)
    {
        if (!empty($value)) {

            $queryBuilder
                ->linkLeft('o.orig_profile', 'op')
                ->linkLeft('op.verificationInfo', 'vi');

            if ($value === VerificationInfo::STATUS_VERIFIED) {
                $queryBuilder->andWhere('vi.status = :viStatus');
            }

            if ($value === VerificationInfo::STATUS_NOT_VERIFIED) {
                $queryBuilder->andWhere('vi.status != :viStatus OR vi.status IS NULL');
            }

            $queryBuilder->setParameter('viStatus', VerificationInfo::STATUS_VERIFIED);

        }
    }
}
