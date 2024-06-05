<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Endpoints\ProductVariant\Patch;

use Qualiteam\SkinActYotpoReviews\Core\Endpoints\Constructor;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\ConstructorInterface;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\Params\SetCurrencyInterface;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\Params\SetExternalIdInterface;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\Params\SetImageUrlInterface;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\Params\SetInventoryQuantityInterface;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\Params\SetNameInterface;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\Params\SetPriceInterface;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\Params\SetSkuInterface;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\Params\SetUrlInterface;
use XC\ProductVariants\Model\ProductVariant;
use Qualiteam\SkinActYotpoReviews\Helpers\ProductVariant as ProductVariantHelper;

class UpdateConstructor implements ConstructorInterface,
    SetCurrencyInterface,
    SetInventoryQuantityInterface,
    SetUrlInterface,
    SetSkuInterface,
    SetExternalIdInterface,
    SetImageUrlInterface,
    SetNameInterface,
    SetPriceInterface
{

    /**
     * @var ProductVariant|null
     */
    private ?ProductVariant $productVariant;

    /**
     * @param \Qualiteam\SkinActYotpoReviews\Core\Endpoints\Constructor                               $constructor
     * @param \Qualiteam\SkinActYotpoReviews\Helpers\ProductVariant $productVariantHelper
     */
    public function __construct(
        private Constructor          $constructor,
        private ProductVariantHelper $productVariantHelper,
    ) {
    }

    /**
     * @param ProductVariant|null $productVariant
     *
     * @return void
     */
    public function prepareProductVariant(?ProductVariant $productVariant): void
    {
        $this->productVariant = $productVariant;
    }

    /**
     * @return array
     */
    public function getBody(): array
    {
        return ['variant' => $this->constructor->getBody()];
    }

    public function build(): void
    {
        $this->constructor->build($this);
    }

    /**
     * @return void
     */
    public function setCurrency(): void
    {
        $this->constructor->addParam(
            self::PARAM_CURRENCY,
            \XLite::getInstance()->getCurrency()->getCode()
        );
    }

    /**
     * @return void
     */
    public function setExternalId(): void
    {
        $this->constructor->addParam(
            self::PARAM_EXTERNAL_ID,
            $this->productVariantHelper->getSku($this->productVariant)
        );
    }

    /**
     * @return void
     */
    public function setImageUrl(): void
    {
        $this->constructor->addParam(
            self::PARAM_IMAGE_URL,
            $this->productVariantHelper->getImageUrl($this->productVariant)
        );
    }

    /**
     * @return void
     */
    public function setInventoryQuantity(): void
    {
        $this->constructor->addParam(
            self::PARAM_INVENTORY_QUANTITY,
            $this->productVariantHelper->getQuantity($this->productVariant)
        );
    }

    /**
     * @return void
     */
    public function setName(): void
    {
        $this->constructor->addParam(
            self::PARAM_NAME,
            $this->productVariantHelper->getName($this->productVariant)
        );
    }

    /**
     * @return void
     */
    public function setPrice(): void
    {
        $this->constructor->addParam(
            self::PARAM_PRICE,
            $this->productVariantHelper->getPrice($this->productVariant)
        );
    }

    /**
     * @return void
     */
    public function setSku(): void
    {
        $this->constructor->addParam(
            self::PARAM_SKU,
            $this->productVariantHelper->getSku($this->productVariant)
        );
    }

    /**
     * @return void
     */
    public function setUrl(): void
    {
        $this->constructor->addParam(
            self::PARAM_URL,
            $this->productVariantHelper->getUrl($this->productVariant)
        );
    }
}