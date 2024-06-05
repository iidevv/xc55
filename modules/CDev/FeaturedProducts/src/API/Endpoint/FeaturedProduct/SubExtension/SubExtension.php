<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\FeaturedProducts\API\Endpoint\FeaturedProduct\SubExtension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use CDev\FeaturedProducts\Model\FeaturedProduct;
use Doctrine\ORM\QueryBuilder;
use XLite\API\Extension\ItemSubExtension\ItemSubExtensionInterface;

class SubExtension implements ItemSubExtensionInterface
{
    /**
     * @var string[]
     */
    protected array $operationNames = ['get'];

    public function support(string $className, string $operationName): bool
    {
        return $className === FeaturedProduct::class && in_array($operationName, $this->operationNames, true);
    }

    public function applyToItem(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        string $operationName = null,
        array $context = []
    ): void {
        $categoryId = $this->getCategoryId($context);
        if (!$categoryId) {
            throw new InvalidArgumentException('Category ID is invalid');
        }

        $productId = $this->getProductId($context);
        if (!$productId) {
            throw new InvalidArgumentException('Product ID is invalid');
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder
            ->innerJoin(sprintf('%s.category', $rootAlias), 'category')
            ->innerJoin(sprintf('%s.product', $rootAlias), 'product')
            ->where('category.category_id = :category_id AND product.product_id = :product_id')
            ->setParameters([
                'category_id' => $categoryId,
                'product_id'  => $productId
            ]);
    }

    protected function getCategoryId(array $context): ?int
    {
        if (preg_match('/categories\/(\d+)\/featured\/\d+/Ss', $context['request_uri'], $match)) {
            return (int) $match[1];
        }

        return null;
    }

    protected function getProductId(array $context): ?int
    {
        if (preg_match('/categories\/\d+\/featured\/(\d+)/Ss', $context['request_uri'], $match)) {
            return (int) $match[1];
        }

        return null;
    }
}
