<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\ConsistencyCheck\Rules\AttributeValue;

use XLite\Model\Repo\Attribute;
use XLite\Core\ConsistencyCheck\Inconsistency;
use XLite\Core\ConsistencyCheck\InconsistencyEntities;
use XLite\Core\ConsistencyCheck\RuleInterface;

class SpecificAttributeValuesRules implements RuleInterface
{
    use AttributeModelStringifier;

    private Attribute $repo;

    public function __construct(Attribute $repo)
    {
        $this->repo = $repo;
    }

    /**
     * @return Inconsistency|boolean
     */
    public function execute()
    {
        $attributes = $this->getAttributesWithoutProducts();

        if ($attributes) {
            $message = \XLite\Core\Translation::getInstance()->translate(
                'There are %model% without valid %another_model% relation',
                [
                    'model'         => 'Product (XLite\Model\Product)',
                    'another_model' => 'Product-Specific Attribute (XLite\Model\Attribute)',
                ]
            );

            return new InconsistencyEntities(
                Inconsistency::ERROR,
                $message,
                array_map(fn ($attr) => $this->stringifyModel($attr), $attributes)
            );
        }

        return false;
    }

    /**
     * @return \XLite\Model\Attribute[]
     */
    protected function getAttributesWithoutProducts(): array
    {
        $qb = $this->repo->createPureQueryBuilder('a');
        $qb->where('a.product IS NOT NULL');

        /** @var \XLite\Model\Attribute[] $attrs */
        $attrs = $qb->getQuery()->getResult();

        $result = [];
        foreach ($attrs as $attr) {
            $attrValues = $attr->getAttributeValues();

            if (empty($attrValues)) {
                $result[] = $attr;
            }
        }

        return $result;
    }
}
