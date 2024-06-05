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
use \XLite\Core\Translation;

class Specification implements ResolverInterface
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
        return $this->mapSpecification($val->productModel, $val->attributes);
    }

    /**
     * @param \XLite\Model\Product      $product
     * @param \XLite\Model\Attribute[]  $attributesModels
     * @param \XLite\Model\ProductClass $productClassModel
     *
     * @return array
     */
    protected function mapSpecification($product, $attributesModels)
    {
        $groups = [        
            'default' => [
                'label'    => '',
                'items'    => [],
            ],
        ];

        $this->mapItem(Translation::lbl('SKU'), $product->getSku(), 'default', $groups);
        $this->mapItem(Translation::lbl('Weight'), static::formatWeight($product->getWeight()), 'default', $groups);

        $this->mapClassAttributes($product, $groups);

        $this->mapGlobalAttributes($product, $groups);
 
        /* Product specific attributes */
        $productAttrs = $attributesModels->toArray();
        $this->mapGroup($product, $productAttrs, 'default', $groups);

        return $groups;
    }

    public static function formatWeight($value)
    {
        return \XLite\View\AView::formatWeight($value);
    }

    protected function mapClassAttributes($product, &$groups)
    {
        $productClassModel = $product->getProductClass();

        if (isset($productClassModel) && !empty($productClassModel)) {

            $attributesFromGroup = [];

            /** @var \XLite\Model\AttributeGroup[] $globalAttrGroups */
            $globalAttrGroups = $productClassModel->getAttributeGroups()->toArray();

            if (!empty($globalAttrGroups)) {
                foreach ($globalAttrGroups as $group) {
                    if ($group->getAttributesCount() > 0) {
                        $groupId = strtolower(
                            str_replace(' ', '_', preg_replace("/[^ _\w]+/", '', $group->getName()))
                        );
                        $groupId = empty($groupId) ? 'default' : $groupId;

                        $groups[$groupId] = [
                            'label'      => $group->getName(),
                            'items' => [],
                        ];
                        foreach ($group->getAttributes() as $attr) {
                            $attributesFromGroup[] = $attr->getId();
                        }

                        $this->mapGroup($product, $group->getAttributes(), $groupId, $groups);
                    }
                }
            }

            /**
             * Fill global attributes that do not have a group
             *
             * @var \XLite\Model\AttributeGroup[] $globalAttrs
             */
            $globalAttrs = $productClassModel->getAttributes()->toArray();

            $globalAttrs = array_filter($globalAttrs, function($attr) use ($attributesFromGroup) {
                return !in_array($attr->getId(), $attributesFromGroup);
            });

            $this->mapGroup($product, $globalAttrs, 'default', $groups);
        }
    }

    protected function mapGlobalAttributes($product, &$groups)
    {
        $globalAttrs = \XLite\Core\Database::getRepo('XLite\Model\Attribute')->findBy(
            ['productClass' => null, 'product' => null]
        );

        $globalAttrGroups = [];
        $globalAttrsNoGroup = [];

        foreach ($globalAttrs as $a) {
            if ($a->getAttributeGroup()) {
                $globalAttrGroups[] = $a->getAttributeGroup();
            } else {
                $globalAttrsNoGroup[] = $a;
            }
        }

        $this->mapGroup($product, $globalAttrsNoGroup, 'default', $groups);
        $this->mapGroups($product, $globalAttrGroups, $groups);
    }

    protected function mapItem($label, $value, $groupid, &$groups)
    {
        $groups[$groupid]['items'][] = [
            'label' => $label,
            'value' => $value,
        ];        
    }

    protected function mapGroup($product, $attrs, $groupid, &$groups)
    {
        if (!empty($attrs)) {
            foreach ($attrs as $attr) {
                if ($this->checkIfProductAttributeIsStaticForJsonApi($product, $attr)) {
                    $groups[$groupid]['items'][] = [
                        'label' => $attr->getName(),
                        'value' => $this->getAttributeValueForJsonApi($product, $attr),
                    ];
                }
            }
        }
    }

    protected function mapGroups($product, $attrGroups, &$groups)
    {
        foreach ($attrGroups as $group) {
            if ($group->getAttributesCount() > 0) {
                $groupId = strtolower(
                    str_replace(' ', '_', preg_replace("/[^ _\w]+/", '', $group->getName()))
                );
                $groupId = empty($groupId) ? 'default' : $groupId;

                $groups[$groupId] = [
                    'label'      => $group->getName(),
                    'items' => [],
                ];

                $this->mapGroup($product, $group->getAttributes(), $groupId, $groups);
            }
        }
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
                return true;
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
