<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\Profile\Builder;

use XLite\Model\Profile;
use XLite\Model\QueryBuilder\AQueryBuilder;
use XLite\Model\Repo\Profile as ProfileRepo;

class AdminCountQueryBuilder implements AdminCountQueryBuilderInterface
{
    protected ProfileRepo $repository;

    public function __construct(ProfileRepo $repository)
    {
        $this->repository = $repository;
    }

    public function build(): AQueryBuilder
    {
        return $this->repository->createQueryBuilder()
            ->bindAdmin()
            ->bindAndCondition('p.status', Profile::STATUS_ENABLED);
    }
}
