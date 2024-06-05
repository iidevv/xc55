<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductQuestions\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * Repository class for the Questions model.
 *
 * @Extender\Mixin
 * @Extender\Depend ("XC\MultiVendor")
 */
class QuestionWithMultiVendors extends \QSL\ProductQuestions\Model\Repo\Question
{
    /**
     * Allowable search params
     */
    // {{{ Search
    public const SEARCH_VENDOR    = 'vendor';
    public const SEARCH_VENDOR_ID = 'vendorId';

    /**
     * Search for unanswered questions.
     *
     * @param boolean              $countOnly Whether to count the number of matching records, or return the whole set of them
     * @param \XLite\Model\Profile $vendor    Vendor profile
     *
     * @return \Doctrine\ORM\PersistentCollection|integer
     */
    public function searchUnansweredVendorQuestions($countOnly, \XLite\Model\Profile $vendor)
    {
        $cnd = $this->defineSearchUnansweredQuestionsCondition();
        if ($vendor) {
            $cnd->{static::SEARCH_VENDOR} = $vendor;
        }

        return $this->search($cnd, $countOnly);
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param \XLite\Model\Profile $value Condition data
     *
     * @return void
     */
    protected function prepareCndVendor(\Doctrine\ORM\QueryBuilder $queryBuilder, \XLite\Model\Profile $value)
    {
        $this->prepareCndVendorId($queryBuilder, $value->getProfileId());
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param integer                    $value        Vendor ID
     *
     * @return void
     */
    protected function prepareCndVendorId(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $queryBuilder->linkInner('q.product', 'p')
            ->linkInner('p.vendor', 'v')
            ->andWhere('v.profile_id = :vendorId')
            ->setParameter('vendorId', (int) $value);
    }
}
