<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\Profile\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use XLite\API\Endpoint\Profile\Builder\AdminCountQueryBuilderInterface;
use XLite\Model\Profile;

class DataPersister implements ContextAwareDataPersisterInterface
{
    protected ContextAwareDataPersisterInterface $inner;

    protected AdminCountQueryBuilderInterface $adminCountQueryBuilder;

    public function __construct(
        ContextAwareDataPersisterInterface $inner,
        AdminCountQueryBuilderInterface $adminCountQueryBuilder
    ) {
        $this->inner = $inner;
        $this->adminCountQueryBuilder = $adminCountQueryBuilder;
    }

    public function supports($data, array $context = []): bool
    {
        return $this->inner->supports($data, $context)
            && $data instanceof Profile;
    }

    /**
     * @param Profile $data
     */
    public function persist($data, array $context = [])
    {
        return $this->inner->persist($data, $context);
    }

    /**
     * @param Profile $data
     */
    public function remove($data, array $context = [])
    {
        if ($data->isAdmin()) {
            if ($this->adminCountQueryBuilder->build()->count() === 1) {
                throw new InvalidArgumentException('Cannot remove the last admin profile');
            }
        }

        return $this->inner->remove($data, $context);
    }
}
