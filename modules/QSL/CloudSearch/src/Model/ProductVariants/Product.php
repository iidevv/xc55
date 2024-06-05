<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CloudSearch\Model\ProductVariants;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Database;

/**
 * The "product" model class
 *
 * @Extender\Mixin
 * @Extender\Depend("XC\ProductVariants")
 */
abstract class Product extends \XLite\Model\Product
{
    protected $constrainCloudSearchProductVariants;

    /**
     * Define default variant
     *
     * @return void
     */
    protected function defineDefaultVariant()
    {
        $defaultVariant = null;

        if ($this->constrainCloudSearchProductVariants !== null) {
            if ($this->mustHaveVariants() && $this->hasVariants()) {
                $filteredVariants = $this->getFilteredCloudSearchVariants();

                $repo = Database::getRepo('\XC\ProductVariants\Model\ProductVariant');
                $defaultVariant = $repo->findOneBy(
                    [
                        'product'      => $this,
                        'defaultValue' => true,
                    ]
                );

                if (!$defaultVariant
                    || $defaultVariant->isOutOfStock()
                    || !$filteredVariants->contains($defaultVariant)
                ) {
                    $minPrice             = $minPriceOutOfStock = false;
                    $defVariantOutOfStock = null;

                    foreach ($filteredVariants as $variant) {
                        if (!$variant->isOutOfStock()) {
                            if (false === $minPrice || $minPrice > $variant->getClearPrice()) {
                                $minPrice   = $variant->getClearPrice();
                                $defaultVariant = $variant;
                            }
                        } elseif (!$defaultVariant) {
                            if (false === $minPriceOutOfStock || $minPriceOutOfStock > $variant->getClearPrice()) {
                                $minPriceOutOfStock   = $variant->getClearPrice();
                                $defVariantOutOfStock = $variant;
                            }
                        }
                    }

                    $defaultVariant = $defaultVariant ?: $defVariantOutOfStock;
                }
            }
        }

        if ($defaultVariant) {
            $this->defaultVariant = $defaultVariant;
        } else {
            parent::defineDefaultVariant();
        }
    }

    /**
     * Get variants
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFilteredCloudSearchVariants()
    {
        $variants = parent::getVariants();

        $variantIds = array_map(function ($v) {
            return $v['id'];
        }, $this->constrainCloudSearchProductVariants);

        $variants = $variants->filter(function ($v) use ($variantIds) {
            return in_array($v->getId(), $variantIds);
        });

        return $variants;
    }

    /**
     * Constrain product variants so that only filtered could be shown on a product list
     *
     * @param $filterVariants
     */
    public function constrainCloudSearchProductVariants($filterVariants)
    {
        $this->defaultVariant = null;

        $this->constrainCloudSearchProductVariants = $filterVariants;
    }

    /**
     * Remove filter constraint set with the above method
     */
    public function unconstrainCloudSearchProductVariants()
    {
        $this->constrainCloudSearchProductVariants = null;
    }
}
