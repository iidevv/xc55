<?php

namespace Qualiteam\SkinActLinkProductsToAttributes\Model;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Product extends \XLite\Model\Product
{
    /**
     * Storage of current main product quantity
     *
     * @var integer
     */
    protected $linkedParentQty = 0;

    /**
     * Storage of linked product quantity string
     *
     * @var string
     */
    protected $linkedProductQtyString = '';

    public function getLinkedParentQty()
    {
        return $this->linkedParentQty;
    }

    public function setLinkedParentQty($linkedParentQty)
    {
        $this->linkedParentQty = $linkedParentQty;
    }

    public function getLinkedProductQtyString()
    {
        return $this->linkedProductQtyString;
    }

    public function setLinkedProductQtyString($linkedProductQtyString)
    {
        $this->linkedProductQtyString = $linkedProductQtyString;
    }


    public function getLinkedProductQtyArray()
    {
        $linkedProductQtyString = $this->getLinkedProductQtyString();
        $result = [];

        if ($linkedProductQtyString) {
            $qtyValues = explode(',', $linkedProductQtyString);

            foreach ($qtyValues as $qtyValue) {
                [$attributeId, $qty] = explode('_', $qtyValue);

                $result[$attributeId] = $qty;
            }

        }
        return $result;
    }

    public function getLinkedProductQty($attributeId)
    {
        $attrQtyArray = $this->getLinkedProductQtyArray();

        return $attrQtyArray[$attributeId] ?? 1;
    }


    /**
     * Prepare attribute values
     *
     * @param array $ids Request-based selected attribute values OPTIONAL
     *
     * @return array
     */
    public function prepareDefaultAttributeValues()
    {
        return $this->executeCachedRuntime(function () {
            $attributeValues = [];
            foreach ($this->getEditableAttributes() as $a) {
                if ($a->getType() === \XLite\Model\Attribute::TYPE_TEXT) {
                    $value     = $a->getAttributeValue($this)->getValue();
                    $attrValue = $a->getAttributeValue($this);

                    $attributeValues[$a->getId()] = [
                        'attributeValue' => $attrValue,
                        'value'          => $value,
                    ];
                } elseif ($a->getType() === \XLite\Model\Attribute::TYPE_CHECKBOX) {

                    $value = $a->getDefaultAttributeValue($this);
                    $attributeValues[$a->getId()] = $value;
                } else {
                    $attributeValues[$a->getId()] = $a->getDefaultAttributeValue($this);
                }
            }

            return $attributeValues;
        }, ['prepareDefaultAttributeValues']);
    }


}