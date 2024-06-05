<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Endpoints\Products\Post;

use Qualiteam\SkinActYotpoReviews\Core\Endpoints\ConstructorInterface;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\Constructor;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\Products\Params\SetBrandInterface;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\Params\SetCurrencyInterface;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\Products\Params\SetDescriptionInterface;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\Params\SetExternalIdInterface;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\Params\SetImageUrlInterface;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\Params\SetInventoryQuantityInterface;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\Params\SetNameInterface;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\Params\SetPriceInterface;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\Params\SetSkuInterface;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\Params\SetUrlInterface;
use Qualiteam\SkinActYotpoReviews\Helpers\Product as ProductHelper;
use XCart\Container;
use XCart\Domain\ModuleManagerDomain;
use XLite\Model\Product;

class CreateConstructor implements ConstructorInterface,
    SetExternalIdInterface,
    SetNameInterface,
    SetDescriptionInterface,
    SetPriceInterface,
    SetSkuInterface,
    SetUrlInterface,
    SetCurrencyInterface,
    SetInventoryQuantityInterface,
    SetBrandInterface,
    SetImageUrlInterface
{
    /**
     * @var \XLite\Model\Product|null
     */
    private ?Product $product;

    /**
     * @var \XCart\Domain\ModuleManagerDomain
     */
    private ModuleManagerDomain $moduleManagerDomain;

    /**
     * @param \Qualiteam\SkinActYotpoReviews\Core\Endpoints\Constructor $constructor
     * @param \Qualiteam\SkinActYotpoReviews\Helpers\Product            $productHelper
     */
    public function __construct(
        private Constructor   $constructor,
        private ProductHelper $productHelper,
    ) {
        $this->moduleManagerDomain = Container::getContainer()?->get(ModuleManagerDomain::class);
    }

    /**
     * @param \XLite\Model\Product|null $product
     *
     * @return void
     */
    public function prepareProduct(?Product $product): void
    {
        $this->product = $product;
    }

    /**
     * @return void
     */
    public function build(): void
    {
        $this->constructor->build($this);
    }

    /**
     * @return array
     */
    public function getBody(): array
    {
        return ['product' => $this->constructor->getBody()];
    }

    /**
     * @return void
     */
    public function setExternalId(): void
    {
        $this->constructor->addParam(
            self::PARAM_EXTERNAL_ID,
            $this->productHelper->getSku($this->product),
        );
    }

    /**
     * @return void
     */
    public function setName(): void
    {
        $this->constructor->addParam(
            self::PARAM_NAME,
            $this->productHelper->getName($this->product)
        );
    }

    /**
     * @return void
     */
    public function setDescription(): void
    {
        $this->constructor->addParam(
            self::PARAM_DESCRIPTION,
            $this->productHelper->getDescription($this->product)
        );
    }

    /**
     * @return void
     */
    public function setPrice(): void
    {
        $this->constructor->addParam(
            self::PARAM_PRICE,
            $this->productHelper->getPrice($this->product)
        );
    }

    /**
     * @return void
     */
    public function setSku(): void
    {
        $this->constructor->addParam(
            self::PARAM_SKU,
            $this->productHelper->getSku($this->product)
        );
    }

    /**
     * @return void
     */
    public function setUrl(): void
    {
        $this->constructor->addParam(
            self::PARAM_URL,
            $this->productHelper->getUrl($this->product)
        );
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
    public function setInventoryQuantity(): void
    {
        $this->constructor->addParam(
            self::PARAM_INVENTORY_QUANTITY,
            $this->productHelper->getQuantity($this->product)
        );
    }

    /**
     * @return void
     */
    public function setBrand(): void
    {
        if ($this->moduleManagerDomain->isEnabled('QSL-ShopByBrand')
            && !empty($this->productHelper->getBrand($this->product))
        ) {
            $this->constructor->addParam(
                self::PARAM_BRAND,
                $this->productHelper->getBrand($this->product)
            );
        }
    }

    /**
     * @return void
     */
    public function setImageUrl(): void
    {
        if (count($this->product->getImages()) > 0) {
            $this->constructor->addParam(
                self::PARAM_IMAGE_URL,
                $this->productHelper->getImageUrl($this->product)
            );
        }
    }
}