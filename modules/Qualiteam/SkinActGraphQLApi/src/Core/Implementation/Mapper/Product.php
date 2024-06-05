<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper;

use Includes\Logger\LoggerFactory;
use Includes\Utils\Module\Manager;
use Qualiteam\SkinActGraphQLApi\Core\UrlHelper;
use XcartGraphqlApi\DTO\ProductDTO;
use XLite\Core\Config;
use XLite\Core\Converter;
use XLite\Core\View\RenderingContextFactory;
use XLite\Core\View\RenderingContextInterface;
use Qualiteam\SkinActGraphQLApi\View\Renderer\ProductDescription;

class Product
{
    /**
     * @var RenderingContextInterface
     */
    protected $renderingContext;

    /**
     * @param \XLite\Model\Product $product
     *
     * @return ProductDTO
     */
    public function mapToDto(\XLite\Model\Product $product, array $fields = [])
    {
        $productDTO = new ProductDTO();

        $productDTO->productModel = $product;

        $productDTO->id = $product->getProductId();
        $productDTO->product_code = $product->getSku();
        $productDTO->product_name = htmlspecialchars_decode($product->getName());
        $productDTO->makeAnOfferState = (bool)$product->getRealMakeAnOfferState();

        $productDTO->short_description = strip_tags($product->getBriefDescription());
        $productDTO->short_description_html = $product->getBriefDescription();
        $productDTO->description = $product->getDescription();
        $productDTO->description_html = $this->renderDescription($product);
        $productDTO->weight = $product->getWeight();
        $productDTO->weight_u = \XLite\View\AView::formatWeight($product->getWeight());
        $productDTO->inventory_enabled = $product->getInventoryEnabled();
        $productDTO->amount = $product->getInventoryEnabled() ? $product->getPublicAmount()
            : $product->getDefaultAmount();
        $productDTO->product_url = $product->getCleanURL() ? \XLite::getInstance()->getShopURL($product->getCleanURL()) : $product->getURL();
        $productDTO->price = $product->getClearPrice();
        $productDTO->display_price = $product->getDisplayPrice();
        $productDTO->enabled = $product->getEnabled();
        $productDTO->available = $product->isPublicAvailable();
        $productDTO->coming_soon = !$product->availableInDate();
        $productDTO->expected_date = Converter::formatTime($product->getArrivalDate());

        $productDTO->condition = $product->getCiaConditionName();
        $productDTO->conditionCode = $product->getCiaCondition();

        $productDTO->showFreeShippingLabel = (
                $product->getFreeShip()
                || $product->isShipForFree()
            ) && $product->getShippable();

        $productDTO->marketPrice = $product->getMarketPrice();

        $productDTO->freeShippingForProMember = $product->hasFreeShippingIcon();

        //newArrival
        $currentDate = \XLite\Base\SuperClass::getUserTime();
        $daysOffset = abs((int)Config::getInstance()->CDev->ProductAdvisor->na_max_days)
            ?: \CDev\ProductAdvisor\Main::PA_MODULE_OPTION_DEFAULT_DAYS_OFFSET;

        $min = Converter::getDayStart($currentDate - $daysOffset * 24 * 60 * 60);
        $max = Converter::getDayEnd($currentDate);

        $arrDate = $product->getArrivalDate();

        $productDTO->newArrival = $arrDate > $min && $arrDate < $max;

        //color swatches
        $colorSwatchesMapper = new ColorSwatches();
        $productDTO->colorSwatches = $colorSwatchesMapper->mapColorSwatches($product);

        $productDTO->video_tabs_info = $product->getVideoTours();

        // TODO Stubs
        $productDTO->review_rate = $product->getAverageRating();
        $productDTO->votes_count = $product->getVotesCount();
        $productDTO->on_sale = false;
        $productDTO->sale_value = 0;
        $productDTO->sale_type = null;
        $productDTO->bookable = false;
        $productDTO->is_wishlisted = false;

        // Complex fields
        $image = $product->getImage();
        $productDTO->small_image_url = $this->mapSmallImage($image);
        $productDTO->image_url = $this->mapFullImage($image);
        $productDTO->images = $this->mapImages(
            $product->getImages()->toArray()
        );

        $productDTO->options = $product->getEditableAttributes();
        $productDTO->attributes = $product->getAttributes();
        $productDTO->specification = $product->getAttributes();

        $productDTO->stickers = [];
        $productDTO->tags = [];
        $productDTO->vendor = null;

        $productDTO->review_list_url = UrlHelper::insertWebAuth($product->getReviewListUrl());
        $productDTO->video_tour_url = Converter::buildFullURL(
            'product_video_tab',
            '',
            ['product_id' => $product->getProductId()],
            \XLite::getCustomerScript()
        );

        $productDTO->categories = array_map(static function ($item) {
            $mapper = new Category();
            return $mapper->mapToDto($item);
        }, $product->getCategories());


        if (Manager::getRegistry()->isModuleEnabled('QSL', 'ProductQuestions')) {
            $productDTO->unreadQuestions = $product->getQuestions()->filter(static function ($item) {
                return trim($item->getAnswer()) === '';
            })->count();
        }

        return $productDTO;
    }

    /**
     * Get product images for JSON API
     *
     * @param \XLite\Model\Base\Image[] $rawImages
     *
     * @return array
     */
    public function mapImages($rawImages)
    {
        $images = [];

        foreach ($rawImages as $image) {
            $images[] = $this->mapFullImage($image);
        }

        return $images;
    }

    /**
     * @param \XLite\Model\Product $product
     *
     * @return string
     */
    protected function renderDescription($product)
    {
        if (!$this->renderingContext) {
            $this->renderingContext = RenderingContextFactory::createContext();
        }

        $params = [
            'product' => $product
        ];

        $widget = new ProductDescription($params);
        $widget->setRenderingContext($this->renderingContext);
        $widget->setWidgetParams($params);
        $widget->init();

        return $widget->getContent();
    }

    /**
     * @param \XLite\Model\Base\Image $image
     *
     * @return string
     */
    protected function mapSmallImage($image)
    {
        return $this->getResizedProductImageUrl(
            $image,
            $this->getSmallImageDimensions()
        );
    }

    /**
     * @param \XLite\Model\Base\Image $image
     *
     * @return string
     */
    protected function mapFullImage($image)
    {
        return $this->getResizedProductImageUrl(
            $image,
            $this->getFullSizeImageDimensions()
        );
    }

    /**
     * Get resized product image URL for JSON API
     *
     * @param \XLite\Model\Base\Image $image Image
     * @param integer $size Image size OPTIONAL
     *
     * @return string
     */
    protected function getResizedProductImageUrl($image, $size = 0)
    {
        if ($image?->isFileExists()) {
            if ($size > 0) {
                $resizedData = $image->getResizedURL($size, $size);

                $url = $resizedData[2];
            } else {
                $url = $image->getFrontURL();
            }
        } else {
            $url = \Includes\Utils\ConfigParser::getOptions(['images', 'default_image']);

            if (!Converter::isURL($url)) {
                $url = \XLite\Core\Layout::getInstance()->getResourceWebPath(
                    $url,
                    \XLite\Core\Layout::WEB_PATH_OUTPUT_URL,
                    \XLite::INTERFACE_WEB,
                    \XLite::ZONE_CUSTOMER
                );
            }
        }

        return $url;
    }

    public function getSmallImageDimensions()
    {
        return Config::getInstance()->Qualiteam->SkinActGraphQLApi->smallImageDimensions;
    }

    public function getFullSizeImageDimensions()
    {
        return Config::getInstance()->Qualiteam->SkinActGraphQLApi->fullImageDimensions;
    }
}
