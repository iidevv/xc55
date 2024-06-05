<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Product
 * @Extender\Mixin
 */
class Product extends \XLite\Controller\Customer\Product
{
    /**
     * Get variant images
     *
     * @return void
     */
    protected function doActionGetVariantImages()
    {
        $data = null;

        if ($this->getProduct()->mustHaveVariants()) {
            $ids = [];
            $attributeValues = trim(\XLite\Core\Request::getInstance()->{\XLite\View\Product\Details\Customer\Widget::PARAM_ATTRIBUTE_VALUES}, ',');

            if ($attributeValues) {
                $attributeValues = explode(',', $attributeValues);
                foreach ($attributeValues as $v) {
                    $v = explode('_', $v);
                    $ids[$v[0]] = $v[1];
                }
            }

            $attributeValues = $this->getProduct()->prepareAttributeValues($ids);

            $productVariant = $attributeValues
                ? $this->getProduct()->getVariant($attributeValues)
                : null;

            if ($productVariant && $productVariant->getImage()) {
                $data = $this->assembleVariantImageData($productVariant->getImage());
            }
        }

        $this->displayJSON($data);
        $this->setSuppressOutput(true);
    }

    /**
     * Assemble variant image data
     *
     * @param \XC\ProductVariants\Model\Image\ProductVariant\Image $image Image
     *
     * @return array
     */
    protected function assembleVariantImageData(\XLite\Model\Base\Image $image)
    {
        $result = [
            'full' => [
                'w'   => $image->getWidth(),
                'h'   => $image->getHeight(),
                'url' => $image->getURL(),
                'alt' => $image->getAlt(),
            ],
        ];

        foreach ($this->getImageSizes() as $name => $sizes) {
            [
                $result[$name]['w'],
                $result[$name]['h'],
                $result[$name]['url'],
                $result[$name]['srcset']
                ] = $image->getResizedURL($sizes[0], $sizes[1]);
            $result[$name]['alt'] = $image->getAlt();

            $result[$name]['srcset'] = $result[$name]['srcset'] && $result[$name]['url'] !== $result[$name]['srcset']
                ? ($result[$name]['srcset'] . ' ' . \XLite\Model\Base\Image::RETINA_RATIO . 'x')
                : '';
        }

        return $result;
    }

    /**
     * Get image sizes
     *
     * @return array
     */
    protected function getImageSizes()
    {
        return [
            'gallery' => [
                60,
                60,
            ],
            'main'    => [
                $this->getDefaultMaxImageSize(true),
                $this->getDefaultMaxImageSize(false),
            ],
        ];
    }
}
