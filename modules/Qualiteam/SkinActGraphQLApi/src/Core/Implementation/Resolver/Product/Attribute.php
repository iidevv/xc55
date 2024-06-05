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

class Attribute implements ResolverInterface
{
    /**
     * @param             $val
     * @param             $args
     * @param ContextInterface $context
     * @param ResolveInfo $info
     *
     * @return mixed
     */
    public function __invoke($val, $args, $context, ResolveInfo $info)
    {
        return $this->mapStaticAttributes($val->productModel, $val->attributes, $val->productModel->getProductClass());
    }

    /**
     * @param \XLite\Model\Product      $product
     * @param \XLite\Model\Attribute[]  $attributesModels
     * @param \XLite\Model\ProductClass $productClassModel
     *
     * @return array
     */
    protected function mapStaticAttributes($product, $attributesModels, $productClassModel)
    {
        $attributesFromGroup = [];
        $attributes = [
            'default' => [
                'id' => 'default',
                'label'         => '',
                'attributes'    => [],
                'pos'           => 0,
            ],
        ];

        if (isset($productClassModel) && !empty($productClassModel)) {
            /** @var \XLite\Model\AttributeGroup[] $globalAttrGroups */
            $globalAttrGroups = $productClassModel->getAttributeGroups()->toArray();

            if (!empty($globalAttrGroups)) {
                foreach ($globalAttrGroups as $group) {
                    if ($group->getAttributesCount() > 0) {
                        $groupId = strtolower(
                            str_replace(' ', '_', preg_replace("/[^ _\w]+/", '', $group->getName()))
                        );
                        $groupId = empty($groupId) ? 'default' : $groupId;

                        $attributes[$groupId] = [
                            'id'         => $groupId,
                            'label'      => $group->getName(),
                            'pos'        => $group->getPosition(),
                            'attributes' => [],
                        ];

                        /** @var \XLite\Model\Attribute $attr */
                        foreach ($group->getAttributes() as $attr) {
                            $attributesFromGroup[] = $attr->getId();

                            if ($this->checkIfProductAttributeIsStaticForJsonApi($product, $attr)) {
                                $attributes[$groupId]['attributes'][] = array(
                                    'label' => $attr->getName(),
                                    'value' => $this->getAttributeValueForJsonApi($product, $attr),
                                    'group' => $attr->getAttributeGroup() ? $attr->getAttributeGroup()->getName() : '',
                                );
                            }
                        }
                    }
                }
            }

            /**
             * Fill global attributes that do not have a group
             *
             * @var \XLite\Model\AttributeGroup[] $globalAttrs
             */
            $globalAttrs = $productClassModel->getAttributes()->toArray();

            if (!empty($globalAttrs)) {
                foreach ($globalAttrs as $attr) {
                    if (
                        !in_array($attr->getId(), $attributesFromGroup)
                        && $this->checkIfProductAttributeIsStaticForJsonApi($product, $attr)
                    ) {
                        $attributes['default']['attributes'][] = array(
                            'label' => $attr->getName(),
                            'value' => $this->getAttributeValueForJsonApi($product, $attr),
                            'group' => $attr->getAttributeGroup() ? $attr->getAttributeGroup()->getName() : '',
                        );
                    }
                }
            }
        }

        /** @var \XLite\Model\Attribute[] $productAttrs */
        $productAttrs = $attributesModels->toArray();

        if (!empty($productAttrs)) {
            foreach ($productAttrs as $i => $attr) {
                if ($this->checkIfProductAttributeIsStaticForJsonApi($product, $attr)) {
                    $attributes['default']['attributes'][] = array(
                        'label' => $attr->getName(),
                        'value' => $this->getAttributeValueForJsonApi($product, $attr),
                        'group' => $attr->getAttributeGroup() ? $attr->getAttributeGroup()->getName() : ''
                    );
                }
            }
        }

        if (empty($attributes['default']['attributes'])) {
            $attributes = array();
        }

        return $attributes;
    }

    /**
     * Check if product attribute is static (non editable)
     *
     * @param \XLite\Model\Product   $product
     * @param \XLite\Model\Attribute $attr Attribute
     *
     * @return boolean
     */
    protected function checkIfProductAttributeIsStaticForJsonApi($product, $attr)
    {
        switch ($attr->getType()) {
            case \XLite\Model\Attribute::TYPE_CHECKBOX:
            case \XLite\Model\Attribute::TYPE_SELECT:
                return !$attr->isMultiple($product);
                break;
            case \XLite\Model\Attribute::TYPE_TEXT:
                return $attr->getAttributeValue($product) ? !$attr->getAttributeValue($product)->getEditable() : true;
                break;
        }

        return false;
    }

    /**
     * Get flat value for the product attribute
     *
     * @param \XLite\Model\Product   $product
     * @param \XLite\Model\Attribute $attr Attribute
     *
     * @return mixed
     */
    protected function getAttributeValueForJsonApi($product, $attr)
    {
        switch ($attr->getType()) {
            case \XLite\Model\Attribute::TYPE_CHECKBOX:
            case \XLite\Model\Attribute::TYPE_SELECT:
                return implode(', ', $attr->getAttributeValue($product, true));
                break;
            case \XLite\Model\Attribute::TYPE_TEXT:
                return $attr->getAttributeValue($product, true);
                break;
        }

        return $attr->getAttributeValue($product, true);
    }
}
