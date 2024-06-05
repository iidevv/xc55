<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XLite\API\Filter;

use ApiPlatform\Core\Bridge\Doctrine\Common\Filter\OrderFilterInterface;
use ApiPlatform\Core\Bridge\Doctrine\Common\Filter\OrderFilterTrait;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use XLite;
use XLite\API\Language;

class AlphabeticalOrderFilter extends OrderFilter
{
    use OrderFilterTrait;

    public function getDescription(string $resourceClass): array
    {
        $description = [];

        $properties = $this->getProperties();
        if ($properties === null) {
            $properties = array_fill_keys($this->getClassMetadata($resourceClass)->getFieldNames(), null);
        }

        foreach ($properties as $property => $propertyOptions) {
            $propertyName = $this->normalizePropertyName($property);
            $description[sprintf('%s[%s]', $this->orderParameterName, $propertyName)] = [
                'property' => $propertyName,
                'type'     => 'string',
                'required' => false,
                'schema'   => [
                    'type' => 'string',
                    'enum' => [
                        strtolower(OrderFilterInterface::DIRECTION_ASC),
                        strtolower(OrderFilterInterface::DIRECTION_DESC),
                    ],
                ],
                'openapi'  => [
                    'description' => 'Sort order',
                ],
            ];
        }

        return $description;
    }

    public function apply(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null, array $context = [])
    {
        if (
            !isset($context['filters'])
            || !isset($context['filters'][$this->orderParameterName])
            || !\is_array($context['filters'][$this->orderParameterName])
        ) {
            $properties = [
                'name' => self::DIRECTION_ASC,
            ];
        } else {
            $properties = $context['filters'][$this->orderParameterName];
        }

        foreach ($properties as $property => $value) {
            $this->filterProperty($property, $value, $queryBuilder, $queryNameGenerator, $resourceClass, $operationName);
        }
    }

    protected function filterProperty(string $property, $direction, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        $this->addSortByTranslation($queryBuilder, $property);
        $queryBuilder->orderBy("calculated$property", $direction);
    }

    protected function addSortByTranslation(QueryBuilder $queryBuilder, string $field): void
    {
        $alias = $queryBuilder->getRootAliases()[0];

        $defaultCode = XLite::getDefaultLanguage();
        $currentCode = Language::getInstance()->getLanguageCode() ?? $defaultCode;

        $this->addTranslationJoins($queryBuilder, $alias, "ct_$field", $currentCode);
        $queryBuilder->addGroupBy("ct_$field.$field");

        if ($currentCode !== $defaultCode) {
            $this->addTranslationJoins($queryBuilder, $alias, "dt_$field", $defaultCode);
            $queryBuilder->addGroupBy("dt_$field.$field");
            $queryBuilder->addSelect("IFNULL(ct_$field.$field,IFNULL(dt_$field.$field,translations.$field)) AS HIDDEN calculated$field");
        } else {
            $queryBuilder->addSelect("IFNULL(ct_$field.$field,translations.$field) AS HIDDEN calculated$field");
        }
    }

    protected function addTranslationJoins(QueryBuilder $queryBuilder, string $alias, string $translationsAlias, string $code): QueryBuilder
    {
        $queryBuilder
            ->linkLeft(
                $alias . '.translations',
                $translationsAlias,
                Join::WITH,
                $translationsAlias . '.code = :lng' . $code
            )
            ->setParameter('lng' . $code, $code);

        return $queryBuilder;
    }
}
