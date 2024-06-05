<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\ProductImage\SubExtension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use Doctrine\ORM\QueryBuilder;
use XLite\API\Extension\ItemSubExtension\ItemSubExtensionInterface;
use XLite\Model\Image\Product\Image;

class SubExtension implements ItemSubExtensionInterface
{
    /**
     * @var string[]
     */
    protected array $operationNames = ['get'];

    public function support(string $className, string $operationName): bool
    {
        return $className === Image::class && in_array($operationName, $this->operationNames, true);
    }

    public function applyToItem(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        string $operationName = null,
        array $context = []
    ): void {
        $imageId = $this->getImageId($context);
        if (!$imageId) {
            throw new InvalidArgumentException('Image ID is invalid');
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->where(sprintf('%s.id = :image_id', $rootAlias))
            ->setParameters(['image_id' => $imageId]);
    }

    protected function getImageId(array $context): ?int
    {
        if (preg_match('/products\/\d+\/images\/(\d+)/Ss', $context['request_uri'], $match)) {
            return (int) $match[1];
        }

        return null;
    }
}
