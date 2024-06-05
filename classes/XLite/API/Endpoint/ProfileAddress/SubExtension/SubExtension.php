<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\ProfileAddress\SubExtension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use Doctrine\ORM\QueryBuilder;
use XLite\API\Extension\CollectionSubExtension\CollectionSubExtensionInterface;
use XLite\API\Extension\ItemSubExtension\ItemSubExtensionInterface;
use XLite\Model\Address;

class SubExtension implements CollectionSubExtensionInterface, ItemSubExtensionInterface
{
    /**
     * @var string[]
     */
    protected array $operationNames = ['get', 'put', 'delete', 'post'];

    public function support(string $className, string $operationName): bool
    {
        return $className === Address::class && in_array($operationName, $this->operationNames, true);
    }

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        string $operationName = null,
        array $context = []
    ): void {
        $this->addWhere($queryBuilder, $context);
    }

    public function applyToItem(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        string $operationName = null,
        array $context = []
    ): void {
        $this->addWhere($queryBuilder, $context);
    }

    protected function addWhere(QueryBuilder $queryBuilder, array $context): void
    {
        $profileID = $this->getProfileId($context);
        if (!$profileID) {
            throw new InvalidArgumentException('Profile ID is invalid');
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder
            ->innerJoin(sprintf('%s.profile', $rootAlias), 'profile')
            ->andWhere('profile.order IS NULL')
            ->andWhere('profile.profile_id = :profile_id')
            ->setParameter('profile_id', $profileID);
    }

    protected function getProfileId(array $context): ?int
    {
        if (!preg_match('/profiles\/(\d+)\/addresses/S', $context['request_uri'], $match)) {
            return null;
        }

        return (int)$match[1];
    }
}
