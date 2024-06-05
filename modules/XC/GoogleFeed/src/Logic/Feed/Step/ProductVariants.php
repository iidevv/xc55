<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GoogleFeed\Logic\Feed\Step;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Converter;
use XC\GoogleFeed\Main;
use XC\GoogleFeed\Model\Attribute;

/**
 * Products step
 *
 * @Extender\Mixin
 * @Extender\Depend("XC\ProductVariants")
 */
class ProductVariants extends \XC\GoogleFeed\Logic\Feed\Step\Products
{
    // {{{ Row processing

    /**
     * Process item
     *
     * @param \XC\ProductVariants\Model\Product $model
     */
    protected function processModel(\XLite\Model\AEntity $model)
    {
        $canProcessVariants = Main::shouldExportDuplicates() || !$this->hasDuplicateVariants($model);
        if ($model->hasVariants() && $canProcessVariants) {
            $this->applyTranslationSettings();
            foreach ($model->getVariants() as $variant) {
                $this->generator->addToRecord($this->getVariantRecord($variant));
            }
            $this->unapplyTranslationSettings();
        } else {
            parent::processModel($model);
        }
    }

    /**
     * @param \XC\ProductVariants\Model\ProductVariant $model
     * @return string
     */
    protected function getVariantTitle(\XC\ProductVariants\Model\ProductVariant $model)
    {
        $attrsString = array_reduce($model->getValues(), static function ($str, $attr) {
            $str .= $attr->asString() . ' ';
            return $str;
        }, '');

        return $model->getProduct()->getName() . ' ' . trim($attrsString);
    }

    /**
     * @param \XC\ProductVariants\Model\ProductVariant $model
     * @return string
     */
    protected function getVariantId(\XC\ProductVariants\Model\ProductVariant $model)
    {
        return $model->getSku() ?: $model->getVariantId();
    }

    /**
     * @param \XC\ProductVariants\Model\ProductVariant $model
     * @return string
     */
    protected function getVariantAvailability(\XC\ProductVariants\Model\ProductVariant $model)
    {
        if (!$model->availableInDate()) {
            return 'preorder';
        }

        return $model->isOutOfStock()
            ? 'out of stock'
            : 'in stock';
    }

    /**
     * @param \XC\ProductVariants\Model\ProductVariant $model
     * @return string
     */
    protected function getVariantLink(\XC\ProductVariants\Model\ProductVariant $model)
    {
        $values = array_reduce($model->getValues(), static function ($obj, $value) {
            $obj[$value->getAttribute()->getId()] = $value->getId();
            return $obj;
        }, []);

        return $model->getProduct()->getProductId()
            ? Main::getShopURL(
                Converter::buildURL(
                    'product',
                    '',
                    [
                        'product_id'       => $model->getProduct()->getProductId(),
                        'attribute_values' => $values
                    ],
                    \XLite::getCustomerScript(),
                    true
                )
            )
            : '';
    }

    /**
     * @param \XLite\Model\Product $model
     * @param int $offset
     * @return array
     */
    protected function getVariantAdditionalImages(\XLite\Model\Product $model, $offset = 0)
    {
        $result = [];

        foreach ($model->getPublicImages() as $image) {
            if ($image) {
                $result[] = mb_substr($image->getGoogleFeedURL(), 0, self::LINK_LENGTH);
            }
        }

        if ($result) {
            $result = array_slice($result, $offset, 9 + $offset);
        }

        return implode("</g:additional_image_link><g:additional_image_link>", $result);
    }

    /**
     * @param \XC\ProductVariants\Model\ProductVariant $model
     * @return string
     */
    protected function getVariantPrice(\XC\ProductVariants\Model\ProductVariant $model)
    {
        $currency = \XLite::getInstance()->getCurrency();
        $parts = $currency->formatParts($model->getDisplayPrice());
        unset($parts['prefix'], $parts['suffix'], $parts['sign']);
        $parts['code'] = ' ' . strtoupper($currency->getCode());

        return implode('', $parts);
    }

    /**
     * @param \XC\ProductVariants\Model\ProductVariant $model
     * @return string
     */
    protected function getVariantMpn(\XC\ProductVariants\Model\ProductVariant $model)
    {
        return $this->getMpn($model) ?: $this->getMpn($model->getProduct());
    }

    /**
     * @param \XC\ProductVariants\Model\ProductVariant $model
     * @return string
     */
    protected function getVariantGtin(\XC\ProductVariants\Model\ProductVariant $model)
    {
        return $this->getGtin($model) ?: $this->getGtin($model->getProduct());
    }

    /**
     * @param \XC\ProductVariants\Model\ProductVariant $model
     * @return array
     */
    protected function getVariantRecord(\XC\ProductVariants\Model\ProductVariant $model)
    {
        $result = [
            'g:id'                => mb_substr($this->getVariantId($model), 0, self::SKU_LENGTH),
            'g:link'              => mb_substr($this->getVariantLink($model), 0, self::LINK_LENGTH),
            'g:title'             => mb_substr($this->getVariantTitle($model), 0, self::TITLE_LENGTH),
            'g:description'       => preg_replace('/[[:cntrl:]]/S', '', mb_substr(trim(strip_tags($model->getProduct()->getProcessedDescription())), 0, self::DESCRIPTION_LENGTH)),
            'g:price'             => $this->getVariantPrice($model),
            'g:availability'      => $this->getVariantAvailability($model),
            'g:condition'         => $this->getCondition($model->getProduct()),
            'g:gtin'              => $this->getVariantGtin($model),
            'g:mpn'               => $this->getVariantMpn($model),
            'g:product_type'      => $this->getProductType($model->getProduct()),
            'g:shipping_weight'   => $this->getWeight($model)
        ];

        foreach ($model->getValues() as $attrValue) {
            /** @var \XC\GoogleFeed\Model\Attribute $attr */
            $attr = $attrValue->getAttribute();

            $attributeGoogleGroup = $attr->getGoogleShoppingGroup();
            if ($attributeGoogleGroup && in_array($attributeGoogleGroup, Attribute::getGoogleShoppingGroups(), true)) {
                $result['g:' . $attributeGoogleGroup] = $attrValue->asString();
            }
        }

        foreach ($model->getProduct()->getGoogleFeedParams() as $attrName => $data) {
            $attr = $data['attr'];
            $value = $data['value'];

            $attributeGoogleGroup = $attr->getGoogleShoppingGroup();
            if ($attributeGoogleGroup && in_array($attributeGoogleGroup, Attribute::getGoogleShoppingGroups(), true)) {
                $result['g:' . $attributeGoogleGroup] = is_object($value) ? $value->asString() : (string)$value;
            }
        }

        if (!$this->checkIfDuplicate($model)) {
            $result['g:item_group_id'] = mb_substr($this->getRecordId($model->getProduct()), 0, self::SKU_LENGTH);
        }

        if ($model->getImage()) {
            $result['g:image_link'] = mb_substr($model->getImage()->getGoogleFeedURL(), 0, self::LINK_LENGTH);
        }

        if ($model->getProduct()->countImages() > 0) {
            if (isset($result['g:image_link'])) {
                $offset = 0;
            } else {
                $result['g:image_link'] = $model->getProduct()->getImage()->getGoogleFeedURL();
                $offset = 1;
            }

            $result['g:additional_image_link'] = $this->getVariantAdditionalImages($model->getProduct(), $offset);
        }

        if (!$model->availableInDate()) {
            $availabilityDate = date('Y-m-d', $model->getProduct()->getArrivalDate()) . 'T' . date('H:i:s', $model->getProduct()->getArrivalDate()) . 'Z';
            $result['g:availability_date'] = $availabilityDate;
        }

        if (empty($result['g:brand']) || (empty($result['g:gtin']) && empty($result['g:mpn']))) {
            $result['g:identifier_exists'] = 'false';
        }

        return $result;
    }

    /**
     * @param \XLite\Model\Product $model
     * @return bool
     */
    protected function hasDuplicateVariants(\XLite\Model\Product $model)
    {
        if ($model->hasVariants()) {
            foreach ($model->getVariants() as $variant) {
                if ($this->checkIfDuplicate($variant)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param \XC\ProductVariants\Model\ProductVariant $model
     * @return bool
     */
    protected function checkIfDuplicate(\XC\ProductVariants\Model\ProductVariant $model)
    {
        foreach ($model->getValues() as $attrValue) {
            /** @var \XC\GoogleFeed\Model\Attribute $attr */
            $attr = $attrValue->getAttribute();

            if (!in_array($attr->getGoogleShoppingGroup(), Attribute::getGoogleShoppingGroups(), true)) {
                return true;
            }
        }

        return false;
    }
}
