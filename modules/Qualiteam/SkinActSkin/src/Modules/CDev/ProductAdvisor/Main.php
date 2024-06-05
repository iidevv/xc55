<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkin\Modules\CDev\ProductAdvisor;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("CDev\ProductAdvisor")
 */
abstract class Main extends \CDev\ProductAdvisor\Main
{
//    /**
//     * Product labels keys
//     */
//    public const PA_MODULE_PRODUCT_LABEL_NEW  = 'orange new-arrival';
//    public const PA_MODULE_PRODUCT_LABEL_SOON = 'grey coming-soon';

    /**
     * Get the "New!" label
     *
     * @param \XLite\Model\Product $product Current product
     *
     * @return array
     */
    public static function getLabels(\XLite\Model\Product $product)
    {
        $result = parent::getLabels($product);

        if (
            $product->isNewProduct()
            && \CDev\ProductAdvisor\View\FormField\Select\MarkProducts::isCatalogEnabled(
                \XLite\Core\Config::getInstance()->CDev->ProductAdvisor->na_mark_with_label
            )
        ) {
            $result[self::PA_MODULE_PRODUCT_LABEL_NEW] = \XLite\Core\Translation::getInstance()->translate('New');
        }

        return $result;
    }

//    /**
//     * Get the "New!" label
//     *
//     * @param \XLite\Model\Product $product Current product
//     *
//     * @return array
//     */
//    public static function getProductPageLabels(\XLite\Model\Product $product)
//    {
//        $result = [];
//
//        if (
//            $product->isNewProduct()
//            && \CDev\ProductAdvisor\View\FormField\Select\MarkProducts::isProductPageEnabled(
//                \XLite\Core\Config::getInstance()->CDev->ProductAdvisor->na_mark_with_label
//            )
//        ) {
//            $result[self::PA_MODULE_PRODUCT_LABEL_NEW] = \XLite\Core\Translation::getInstance()->translate('New!');
//        }
//
//        if (
//            $product->isUpcomingProduct()
//            && \CDev\ProductAdvisor\View\FormField\Select\MarkProducts::isProductPageEnabled(
//                \XLite\Core\Config::getInstance()->CDev->ProductAdvisor->cs_mark_with_label
//            )
//        ) {
//            $result[self::PA_MODULE_PRODUCT_LABEL_SOON]
//                = \XLite\Core\Translation::getInstance()->translate(
//                    'Expected on X',
//                    ['date' => \XLite\Core\Converter::getInstance()->
//                        formatDate(
//                            $product->getArrivalDate()
//                        )
//                    ]
//                );
//        }
//
//        return $result;
//    }


}
