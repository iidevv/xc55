<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// vim: set ts=4 sw=4 sts=4 et:

namespace Qualiteam\SkinActMagicImages\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender as Extender;
use XLite\Core\Database;

/**
 * Product
 *
 * @Extender\Mixin
 */
class Product extends \XLite\Model\Product
{
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Qualiteam\SkinActMagicImages\Model\MagicSwatchesSet", mappedBy="product",
     *                                                                                    cascade={"all"})
     */
    protected $magicSwatchesSet;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = [])
    {
        $this->magicSwatchesSet = new ArrayCollection();

        parent::__construct($data);
    }

    public function addMagicSwatchesSet(MagicSwatchesSet $magicSwatchesSet)
    {
        $this->magicSwatchesSet[] = $magicSwatchesSet;

        return $this;
    }

    public function getColorSwatchAttributesIds(): array
    {
        $result = [];

        foreach ($this->getColorSwatchAttributes() as $a) {
            $result[] = $a->getId();
        }
        sort($result);

        return $result;
    }

    /**
     * @return array
     */
    public function getColorSwatchAttributes(): array
    {
        $result     = [];
        $attributes = empty($this->getEditableAttributes())
            ? $this->getSingleColorSwatchAttributes()
            : $this->getEditableAttributes();

        if ($attributes) {

            /** @var \XLite\Model\Attribute $attribute */
            foreach ($attributes as $attribute) {
                if ($attribute->isColorSwatchesAttribute()) {

                    /** @var \QSL\ColorSwatches\Model\AttributeValue\AttributeValueSelect $value */
                    foreach ($attribute->getAttributeValue($this) as $value) {
                        $result[] = $value;
                    };
                }
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    protected function getSingleColorSwatchAttributes(): array
    {
        $attributesS = $this->getAttributeValueS();
        $result      = [];

        /** @var \XLite\Model\AttributeValue\AttributeValueSelect $attr */
        foreach ($attributesS as $attr) {
            $result[] = $attr->getAttribute();
        }

        return $result;
    }

    /**
     * @return bool
     */
    public function hasMagicSwatchesSet(): bool
    {
        return count($this->getMagicSwatchesSet()) > 0;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection|\Doctrine\Common\Collections\Collection
     */
    public function getMagicSwatchesSet(): ArrayCollection|\Doctrine\Common\Collections\Collection
    {
        return $this->magicSwatchesSet;
    }

    public function cloneEntity()
    {
        $newProduct = parent::cloneEntity();

        $this->cloneEntityMagicSwatchesSet($newProduct);

        return $newProduct;
    }

    protected function cloneEntityMagicSwatchesSet(Product $newProduct): void
    {
        $magicSets = $this->getMagicSwatchesSet();
        $newAttributes = [];

        /** @var \XLite\Model\AttributeValue\AttributeValueSelect $colorSwatchAttribute */
        foreach ($newProduct->getColorSwatchAttributes() as $colorSwatchAttribute) {
            $newAttributes[$colorSwatchAttribute->getAttributeOption()->getName()] = $colorSwatchAttribute->getId();
        }

        /** @var \Qualiteam\SkinActMagicImages\Model\MagicSwatchesSet $magicSet */
        foreach ($magicSets as $magicSet) {
            $newMagicSet = $magicSet->cloneEntity();
            $newMagicSet->setProduct($newProduct);

            if ($newMagicSet->getAttributeValue()) {
                $newAttributeName = $newMagicSet->getAttributeValue()->getAttributeOption()->getName();

                $newMagicSet->setAttributeValue(
                    $newAttributes[$newAttributeName]
                );
            }

            $newProduct->addMagicSwatchesSet($newMagicSet);
        }
    }
}
