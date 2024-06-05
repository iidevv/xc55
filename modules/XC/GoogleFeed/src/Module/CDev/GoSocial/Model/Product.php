<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GoogleFeed\Module\CDev\GoSocial\Model;

use XCart\Extender\Mapping\Extender;

/**
 * Product
 *
 * @Extender\Mixin
 * @Extender\Depend("CDev\GoSocial")
 */
class Product extends \XLite\Model\Product
{
    /**
     * @return array
     */
    protected function defineAdditionalMetaTags()
    {
        return parent::defineAdditionalMetatags() + $this->getOgProductCategory();
    }

    /**
     * @return array
     */
    protected function getOgProductCategory()
    {
        return $this->executeCachedRuntime(function () {
            $result = [];

            if ($this->getProductId()) {
                $attrs = $this->defineGoogleFeedAttributes();

                foreach ($attrs as $attr) {
                    if ($attr->getGoogleShoppingGroup() == 'google_product_category') {
                        $attrValue = $attr->getAttributeValue($this, true);
                        if (is_array($attrValue)) {
                            $attrValue = reset($attrValue);
                        }

                        $result['product:category'] = is_object($attrValue)
                            ? $attrValue->asString()
                            : (string) $attrValue;

                        break;
                    }
                }
            }

            return $result;
        }, ['google_product_category', $this->getProductId()]);
    }
}
