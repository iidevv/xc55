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

class TranslationAwareOrderFilter extends OrderFilter
{
    use OrderFilterTrait;

    /**
     * @var bool Default sort already overridden
     */
    protected bool $defaultSortOverridden = false;

    public function getDescription(string $resourceClass): array
    {
        $description = [];

        $properties = $this->getProperties();
        if ($properties === null) {
            $properties = array_fill_keys($this->getClassMetadata($resourceClass)->getFieldNames(), null);
        }

        foreach ($properties as $property => $propertyOptions) {
            if (
                !$this->isPropertyMapped($property, $resourceClass)
                && !$this->isTranslationPropertyMapped($property, $resourceClass)
            ) {
                continue;
            }
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
                    'description' => "Sort order by $propertyName",
                ],
            ];
        }

        return $description;
    }

    protected function isTranslationPropertyMapped(string $property, string $resourceClass): bool
    {
        if (!is_subclass_of($resourceClass, 'XLite\Model\Base\I18n')) {
            return false;
        }

        $metadata = $this->getClassMetadata($resourceClass . 'Translation');

        return $metadata->hasField($property);
    }

    protected function filterProperty(string $property, $direction, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        if ($this->isPropertyEnabled($property, $resourceClass) && $this->isPropertyMapped($property, $resourceClass)) {
            $direction = $this->normalizeValue($direction, $property);
            if ($direction === null) {
                return;
            }

            $alias = $queryBuilder->getRootAliases()[0];
            $field = $property;

            if ($this->isPropertyNested($property, $resourceClass)) {
                [$alias, $field] = $this->addJoinsForNestedProperty($property, $alias, $queryBuilder, $queryNameGenerator, $resourceClass, Join::LEFT_JOIN);
            }

            if (($nullsComparison = $this->properties[$property]['nulls_comparison'] ?? null) !== null) {
                $nullsDirection = self::NULLS_DIRECTION_MAP[$nullsComparison][$direction];

                $nullRankHiddenField = sprintf('_%s_%s_null_rank', $alias, str_replace('.', '_', $field));

                $queryBuilder->addSelect(sprintf('CASE WHEN %s.%s IS NULL THEN 0 ELSE 1 END AS HIDDEN %s', $alias, $field, $nullRankHiddenField));

                if ($this->defaultSortOverridden) {
                    $queryBuilder->addOrderBy($nullRankHiddenField, $nullsDirection);
                } else {
                    $queryBuilder->orderBy($nullRankHiddenField, $nullsDirection);
                    $this->defaultSortOverridden = true;
                }
            }

            if ($this->defaultSortOverridden) {
                $queryBuilder->addOrderBy(sprintf('%s.%s', $alias, $field), $direction);
            } else {
                $queryBuilder->orderBy(sprintf('%s.%s', $alias, $field), $direction);
                $this->defaultSortOverridden = true;
            }
        }

        if ($this->isTranslationPropertyMapped($property, $resourceClass)) {
            $this->addSortByTranslation($queryBuilder, $property);

            if ($this->defaultSortOverridden) {
                $queryBuilder->addOrderBy("calculated$property", $direction);
            } else {
                $queryBuilder->orderBy("calculated$property", $direction);
                $this->defaultSortOverridden = true;
            }
        }
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
