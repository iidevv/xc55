<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// vim: set ts=4 sw=4 sts=4 et:

namespace Qualiteam\SkinActMagicImages\View\Product\Details\Customer;

use Qualiteam\SkinActMagicImages\Traits\MagicImagesTrait;
use XCart\Extender\Mapping\ListChild;
use XLite\Core\Request;

/**
 * Gallery
 *
 * @ListChild (list="product.details.page.image.gallery.before", weight="10")
 */
class Gallery extends \XLite\View\Product\Details\Customer\Widget
{
    use MagicImagesTrait;

    public const QUICKLOOK_PAGE        = 'product.details.quicklook.image';
    public const QUICKLOOK_IMAGE_WIDTH = 300;

    public const QUICKLOOK_IMAGE_HEIGHT = 300;

    public function getFingerprint()
    {
        return 'widget-fingerprint-gallery';
    }

    public function getJSFiles()
    {
        $list   = parent::getJSFiles();
        $list[] = $this->getModulePath() . '/gallery.js';

        return $list;
    }

    protected function getSwatchMagicImages()
    {
        $magicSetImages = $this->getProduct()->getMagicSwatchesSet();

        /** @var \Qualiteam\SkinActMagicImages\Model\MagicSwatchesSet $set */
        foreach ($magicSetImages as $set) {
            if (!$set->getAttributeValue()) {
                return $set;
            }

            if (in_array($set->getAttributeValue()->getId(), $this->getAttributeValuesInRequest())) {
                return $set;
            }
        }

        return $magicSetImages ? $magicSetImages[0] : null;
    }

    protected function getAttributeValuesInRequest()
    {
        $attributeValuesArr = $this->prepareAttributeValuesByRequest();
        $values             = [];

        foreach ($attributeValuesArr as $value) {
            $result             = explode('_', $value);
            $values[$result[0]] = $result[1];
        }

        return $values;
    }

    protected function prepareAttributeValuesByRequest(): array
    {
        $attributeValuesArr = explode(',', Request::getInstance()->attribute_values);

        return array_filter($attributeValuesArr, function ($item) {
            return !empty($item);
        });
    }

    protected function getDefaultTemplate()
    {
        return $this->getModulePath() . '/product/details/parts/common.gallery.twig';
    }

    protected function getWidgetMaxWidth()
    {
        return $this->viewListName == static::QUICKLOOK_PAGE
            ? static::QUICKLOOK_IMAGE_WIDTH
            : \XLite::getController()->getDefaultMaxImageSize();
    }

    /**
     * Get product image container max height
     *
     * @return boolean
     */
    protected function getWidgetMaxHeight()
    {
        return $this->viewListName == static::QUICKLOOK_PAGE
            ? static::QUICKLOOK_IMAGE_HEIGHT
            : \XLite::getController()->getDefaultMaxImageSize(false);
    }
}
