<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGoogleFeedAdvanced\Model\Repo;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Query\Expr\Join;
use Qualiteam\SkinActGoogleFeedAdvanced\Main;
use XCart\Extender\Mapping\Extender;
use XLite\Model\Attribute;

/**
 * @Extender\Mixin
 */
class Product extends \XLite\Model\Repo\Product
{
    protected function prepareCndOrderBy(\Doctrine\ORM\QueryBuilder $queryBuilder, array $value)
    {
        if (!$this->isCountSearchMode()) {
            [$sort, $order] = $this->getSortOrderValue($value);

            if (in_array($sort, Main::getGoogleAttributes(), true)) {
                $this->addGoogleAttributesCondition($queryBuilder, $sort, $order);
                return;
            }
        }

        parent::prepareCndOrderBy($queryBuilder, $value);
    }

    protected function addGoogleAttributesCondition(\Doctrine\ORM\QueryBuilder $queryBuilder, $sort, $order)
    {
        $attr = Main::getAttributeByName($sort);

        if ($attr) {
            $rootAlias = $queryBuilder->getRootAliases()[0];
            $doWorkWithQuery = false;

            switch ($attr->getType()) {
                case Attribute::TYPE_SELECT:
                    $queryBuilder
                        ->linkLeft(
                            sprintf('%s.attributeValueS', $rootAlias),
                            $sort . '_attribute_values',
                            Join::WITH,
                            $sort . '_attribute_values.attribute = :attributeId'
                        );
                    $doWorkWithQuery = true;
                    break;

                case Attribute::TYPE_HIDDEN:
                    $queryBuilder
                        ->linkLeft(
                            sprintf('%s.attributeValueH', $rootAlias),
                            $sort . '_attribute_values',
                            Join::WITH,
                            $sort . '_attribute_values.attribute = :attributeId'
                        );
                    $doWorkWithQuery = true;
                    break;
            }

            if ($doWorkWithQuery) {
                $queryBuilder
                    ->linkLeft(
                        $sort . '_attribute_values.attribute_option',
                        $sort . '_attribute_option'
                    )
                    ->linkLeft(
                        $sort . '_attribute_option.translations',
                        $sort . '_attribute_option_translation',
                        Join::WITH,
                        $sort . '_attribute_option_translation.code = :attribute_option_translation_code'
                    )
                    ->addSelect('IFELSE(' . $sort . '_attribute_values.id IS NULL, :empty_value, ' . $sort . '_attribute_option_translation.name) AS ' . $sort . '_attribute_option_translation_name')
                    ->orderBy($sort . '_attribute_option_translation_name', $order)
                    ->setParameter('attributeId', $attr->getId())
                    ->setParameter('attribute_option_translation_code', \XLite::getDefaultLanguage())
                    ->setParameter('empty_value', '');
            }
        }
    }
}
