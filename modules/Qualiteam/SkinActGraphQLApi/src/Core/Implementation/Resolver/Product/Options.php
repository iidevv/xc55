<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Resolver\Product;

use GraphQL\Type\Definition\ResolveInfo;
use XcartGraphqlApi\ContextInterface;
use XcartGraphqlApi\Resolver\ResolverInterface;
use XLite\Model\AttributeValue\Multiple;
use XcartGraphqlApi\DTO\ProductDTO;

class Options implements ResolverInterface
{
    /**
     * @param ProductDTO  $val
     * @param             $args
     * @param ContextInterface $context
     * @param ResolveInfo $info
     *
     * @return mixed
     */
    public function __invoke($val, $args, $context, ResolveInfo $info)
    {
        return $this->mapOptions(
            $val->id,
            $val->options
        );
    }

    /**
     * Get product options for JSON API
     *
     * @param int                      $productId
     * @param \XLite\Model\Attribute[] $optionsModels
     *
     * @return array
     */
    protected function mapOptions($productId, $optionsModels)
    {
        $options = [];

        /**
         * @var \XLite\Model\Attribute $attr
         */
        foreach ($optionsModels as $attr) {
            $options[] = [
                'id'     => $attr->getId(),
                'option_name'   => $attr->getName(),
                'option_type'   => $this->mapTypeOfAttribute($attr->getType()),
                'options'       => $this->mapOptionValues(
                    $productId,
                    $attr,
                    $attr->getAttributeValues()
                ),
            ];
        }

        return $options;
    }

    /**
     * @param $origType
     *
     * @return string
     */
    protected function mapTypeOfAttribute($origType)
    {
        switch($origType) {
            case \XLite\Model\Attribute::TYPE_CHECKBOX:
                $type = 'checkbox';
                break;
            case \XLite\Model\Attribute::TYPE_TEXT:
                $type = 'text';
                break;
            default:
                $type = 'select';
                break;
        }

        return $type;
    }

    /**
     * @param integer                                       $productId
     * @param \XLite\Model\Attribute                        $attribute
     * @param \XLite\Model\AttributeValue\AAttributeValue[] $attrValues
     *
     * @return array
     */
    protected function mapOptionValues($productId, $attribute, $attrValues)
    {
        $option_values = array();

        foreach ($attrValues as $value) {
            /**
             * TODO: global attributes return values for all products that are set to use them, consider refactoring the code to pull data via query builder and do product filtering in MySQL to prevent potential performance loss
             */
            if ($productId !== $value->getProduct()->getProductId()) {
                continue;
            }

            $option = array(
                'id'    => $value->getId(),
                'value' => $value->asString(),
            );

            switch ($attribute->getType()) {
                case \XLite\Model\Attribute::TYPE_CHECKBOX:
                case \XLite\Model\Attribute::TYPE_SELECT:
                    $option['default'] = $value->isDefault();
                    $option['modifier_type'] = $value->getPriceModifierType() === Multiple::TYPE_ABSOLUTE
                        ? 'absolute'
                        : 'percent';
                    $option['modifier_value'] = $value->getPriceModifier();
                    break;
                default:
                    $option['default'] = true;
                    $option['modifier_type'] = 'absolute';
                    $option['modifier_value'] = 0;
                    break;
            }

            $option_values[] = $option;
        }

        return $option_values;
    }
}
