<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\Category\Filter;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractContextAwareFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\PropertyInfo\Type;
use XLite\Core\Database;

class ParentFilter extends AbstractContextAwareFilter
{
    public function apply(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null, array $context = [])
    {
        if (!isset($context['filters']['parent'])) {
            $context['filters']['parent'] = Database::getRepo('XLite\Model\Category')->getRootCategoryId();
        }

        foreach ($context['filters'] as $property => $value) {
            $this->filterProperty($this->denormalizePropertyName($property), $value, $queryBuilder, $queryNameGenerator, $resourceClass, $operationName, $context);
        }
    }

    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        if ($property !== 'parent') {
            return;
        }

        $alias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->innerJoin($alias . '.parent', 'cparent')
            ->andWhere('cparent.category_id = :parentId')
            ->setParameter('parentId', $value);
    }

    public function getDescription(string $resourceClass): array
    {
        if (!$this->properties) {
            return [];
        }

        $description = [];
        foreach ($this->properties as $property => $propertyOptions) {
            $description[$property] = [
                'property' => $property,
                'type' => Type::BUILTIN_TYPE_INT,
                'required' => false,
                'openapi' => [
                    'description' => 'ID of the parent category. Leave empty to get the list of root categories.',
                ],
            ];
        }

        return $description;
    }
}
