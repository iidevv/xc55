<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\InputRule\SubRule;

use ApiPlatform\Core\Exception\InvalidArgumentException;
use XLite\Model\QueryBuilder\AQueryBuilder;
use XLite\Model\Repo\ARepo;

class CheckUniqueField implements SubRuleInterface
{
    protected ARepo $repository;

    protected string $entityFieldName;

    protected string $inputFieldName;

    public function __construct(
        ARepo $repository,
        string $entityFieldName,
        ?string $inputFieldName = null
    ) {
        $this->repository = $repository;
        $this->entityFieldName = $entityFieldName;
        $this->inputFieldName = $inputFieldName ?: $entityFieldName;
    }

    public function check(object $inputDTO, array $context): void
    {
        // Only POST
        if ($context['operation_type'] !== 'collection' || !empty($context['object_to_populate'])) {
            return;
        }

        if ($this->buildQueryBuilder($inputDTO, $context)->count() === 0) {
            return;
        }

        throw new InvalidArgumentException($this->buildMessage($inputDTO));
    }

    protected function buildQueryBuilder(object $inputDTO, array $context): AQueryBuilder
    {
        return $this->repository->createQueryBuilder('o')
            ->andWhere(sprintf('o.%s = :%s', $this->entityFieldName, $this->entityFieldName))
            ->setParameter($this->entityFieldName, $inputDTO->{$this->inputFieldName});
    }

    protected function buildMessage(object $inputDTO): string
    {
        return sprintf('Field "%s" must be unique', $this->inputFieldName);
    }
}
