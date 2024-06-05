<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Wholesale\View;

use CDev\Wholesale\Module\XC\ProductVariants\Model\ProductVariantWholesalePrice;
use Includes\Utils\Module\Manager;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Database;

/**
 * @Extender\Mixin
 */
class ProductPageCollection extends \XLite\View\ProductPageCollection
{
    /**
     * Register the view classes collection
     *
     * @return array
     */
    protected function defineWidgetsCollection()
    {
        return array_merge(
            parent::defineWidgetsCollection(),
            [
                'CDev\Wholesale\View\ProductPrice',
                'XLite\View\Product\Details\Customer\EditableAttributes',
            ]
        );
    }

    /**
     * Check - allowed display subwidget or not
     *
     * @param string $name Widget class name
     *
     * @return boolean
     */
    protected function isAllowedWidget($name)
    {
        $result = parent::isAllowedWidget($name);

        if ($result) {
            switch ($name) {
                case '\CDev\Wholesale\View\ProductPrice':
                    $types = $this->getProductModifierTypes();
                    if (empty($types['wholesalePrice'])) {
                        $result = false;
                    }
                    break;

                default:
            }
        }

        return $result;
    }

    /**
     * Get product modifier types
     *
     * @return array
     */
    protected function getProductModifierTypes()
    {
        $additional = null;
        $additionalVariants = null;
        $wholesale = null;

        if (!isset($this->productModifierTypes)) {
            if (Manager::getRegistry()->isModuleEnabled('XC', 'ProductVariants')) {
                // ProductVariants module detected
                $additional = Database::getRepo('XC\ProductVariants\Model\ProductVariant')
                    ->getModifierTypesByProduct($this->getProduct());
                $additionalVariants = Database::getRepo(ProductVariantWholesalePrice::class)
                    ->getModifierTypesByProduct($this->getProduct());
            }
            if (empty($additional['price']) || empty($additionalVariants['price']) || empty($additionalVariants['wholesalePrice'])) {
                // ProductVariants module is not detected or product has not variants
                $wholesale = Database::getRepo('CDev\Wholesale\Model\WholesalePrice')
                    ->getModifierTypesByProduct($this->getProduct());
            }
        }

        $result = parent::getProductModifierTypes();

        foreach ([$additional, $additionalVariants, $wholesale] as $modifierTypes) {
            if (isset($modifierTypes)) {
                foreach ($modifierTypes as $key => $value) {
                    $result[$key] = isset($result[$key])
                        ? $result[$key] || $value
                        : $value;
                }

                if (!$result['price'] && $modifierTypes['price']) {
                    $result['price'] = true;
                }

                $this->productModifierTypes = $result;
            }
        }

        return $result;
    }
}
