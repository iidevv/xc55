<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFrequentlyBoughtTogether\View;

use Qualiteam\SkinActFrequentlyBoughtTogether\Traits\FreqBoughtTogetherTrait;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Auth;
use XLite\Core\CommonCell;
use XLite\Core\Config;
use XLite\Core\PreloadedLabels\ProviderInterface;
use XLite\Model\OrderItem;
use XLite\Model\Product;
use XLite\View\Product\ListItem;

/**
 * @Extender\Mixin
 */
class FrequentlyBoughtTogether extends \CDev\ProductAdvisor\View\BoughtBought implements ProviderInterface
{
    use FreqBoughtTogetherTrait;

    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = $this->getModulePath() . '/js/controller.js';
        $list[] = $this->getModulePath() . '/js/elements.js';

        return $list;
    }

    /**
     * Get CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = [
            'file'  => $this->getModulePath() . '/css/less/style.less',
            'media' => 'screen',
            'merge' => 'bootstrap/css/bootstrap.less',
        ];
        return $list;
    }

    /**
     * Is visible block
     *
     * @return bool
     */
    public function isVisible()
    {
        return parent::isVisible()
            && $this->getVisibleCondition();
    }

    /**
     * Get a conditions for block visibility
     *
     * @return bool
     */
    public function getVisibleCondition(): bool
    {
        return $this->getOrderIdsCount() > 0
            && $this->getFreqBoughtTogetherProducts()
            && $this->isShowFreqBoughtProducts();
    }

    /**
     * Get an order ids count
     *
     * @return int
     */
    protected function getOrderIdsCount(): int
    {
        return count($this->getOrderIds($this->getProductId()));
    }

    /**
     * Get an order ids
     *
     * @param $productId
     *
     * @return mixed
     */
    protected function getOrderIds($productId)
    {
        return $this->executeCachedRuntime(
            static function () use ($productId) {
                return \XLite\Core\Database::getRepo(OrderItem::class)->findFreqBoughtProductsOrderIds($productId);
            },
            [__CLASS__, self::class, __METHOD__, $productId]
        );
    }

    /**
     * Get a frequently bought together products
     *
     * @return array
     */
    protected function getFreqBoughtTogetherProducts(): array
    {
        $data = $this->getPageData();

        if ($this->isCorrectExcludeFreqBoughtTogether()) {
            $data = $this->correctExcludeFreqBoughtTogether($data);
        }

        if ($this->isCorrectFreqBoughtTogetherProductsPosition()) {
            $data = $this->correctFreqBoughtTogetherProductsPosition($data, $this->getProductId());
        }

        return $data;
    }

    /**
     * Is correcting a product array where check excluded products
     *
     * @return bool
     */
    protected function isCorrectExcludeFreqBoughtTogether(): bool
    {
        return true;
    }

    /**
     * Is correcting a product array where current product must be shown first
     *
     * @return bool
     */
    protected function isCorrectFreqBoughtTogetherProductsPosition(): bool
    {
        return true;
    }

    /**
     * Is show a frequently bought together products
     *
     * @return bool
     */
    protected function isShowFreqBoughtProducts(): bool
    {
        return $this->isOnlyOneFreqBoughtProduct()
            ? $this->isShowBlockIfOneFreqBoughtProduct()
            : $this->getFreqBoughtTogetherProductsCount() > 1;
    }

    /**
     * Is only one frequently bought product in an array
     *
     * @return bool
     */
    protected function isOnlyOneFreqBoughtProduct(): bool
    {
        return $this->getFreqBoughtTogetherProductsCount() === 1;
    }

    /**
     * Get a frequently bought together products count
     *
     * @return int
     */
    protected function getFreqBoughtTogetherProductsCount(): int
    {
        return count($this->getFreqBoughtTogetherProducts());
    }

    /**
     * Show a block frequently bought product if the array has only one product
     * or hidden if the product in array eq a current product
     *
     * @return bool
     */
    protected function isShowBlockIfOneFreqBoughtProduct(): bool
    {
        $products = $this->getFreqBoughtTogetherProducts();
        $product  = reset($products);

        return $product->getProductId() !== $this->getProductId();
    }

    /**
     * Get css classes
     *
     * @return string
     */
    public function getListCSSClasses()
    {
        return 'items-list items-list-products frequently-bought-together-products';
    }

    /**
     * Get widget for a total price block
     *
     * @return string
     */
    public function getTotalProductWidgetContent()
    {
        return $this->getChildWidget(
            $this,
            $this->getTotalWidgetParams()
        )->getContent();
    }

    /**
     * Get params for a widget total block
     *
     * @return array
     */
    protected function getTotalWidgetParams()
    {
        return [
            ListItem::PARAM_TEMPLATE                => $this->getTotalBlockTemplate(),
            ListItem::PARAM_DISPLAY_MODE            => $this->getDisplayMode(),
            ListItem::PARAM_ITEM_LIST_WIDGET_TARGET => static::getWidgetTarget(),
        ];
    }

    /**
     * Get a path for a widget template
     *
     * @return string
     */
    public function getTotalBlockTemplate(): string
    {
        return $this->getDir() . '/' . $this->getPageBodyDir() . '/total.twig';
    }

    /**
     * Get a template dir
     *
     * @return string
     */
    protected function getDir(): string
    {
        return $this->getModulePath() . '/' . parent::getDir();
    }

    /**
     * Get preload language labels
     *
     * @return array
     */
    public function getPreloadedLanguageLabels()
    {
        return [
            'Freq bought selected items add to cart'     => static::t('SkinActFrequentlyBoughtTogether add selected items to cart'),
            'Freq bought selected items add to wishlist' => static::t('SkinActFrequentlyBoughtTogether add selected items to wishlist'),
        ];
    }

    /**
     * Get a label for "add to cart" button
     *
     * @return string
     */
    public function getFreqBoughtAdd2CartLabel(): string
    {
        return static::t('SkinActFrequentlyBoughtTogether add selected items to cart', [
            'itemsCount' => $this->getFreqBoughtTogetherBlockItemsCount() ?? $this->getDefaultFreqBoughtTogetherBlockItemsCount(),
        ]);
    }

    /**
     * Get a frequently bought together items count in the block
     *
     * @return int|null
     */
    protected function getFreqBoughtTogetherBlockItemsCount(): ?int
    {
        return $this->getFreqBoughtTogetherProductsCount() < $this->getDefaultFreqBoughtTogetherBlockItemsCount()
            ? $this->getFreqBoughtTogetherProductsCount()
            : null;
    }

    /**
     * Get a default frequently bought together items count in the block
     *
     * @return int
     */
    protected function getDefaultFreqBoughtTogetherBlockItemsCount(): int
    {
        return Config::getInstance()->CDev->ProductAdvisor->cbb_max_count_in_block ?? $this->getMaxItemsInBlockCount();
    }

    /**
     * Get a label for "add to wishlist" button
     *
     * @return string
     */
    public function getFreqBoughtAdd2WishlistLabel(): string
    {
        return static::t('SkinActFrequentlyBoughtTogether add selected items to wishlist', [
            'itemsCount' => $this->getFreqBoughtTogetherBlockItemsCount() ?? $this->getDefaultFreqBoughtTogetherBlockItemsCount(),
        ]);
    }

    /**
     * Get a label for "add to wishlist" button for anonymous user
     *
     * @return string
     */
    public function getFreqBoughtLoginToAddWishlist(): string
    {
        return static::t('SkinActFrequentlyBoughtTogether login to add wishlist');
    }

    /**
     * Get a price frequently bought together products
     *
     * @return float
     */
    public function getFreqBoughtProductsTotalPrice(): float
    {
        $products = $this->getFreqBoughtTogetherProducts();
        $price    = 0;

        foreach ($products as $product) {
            $price += $product->getDisplayPrice();
        }

        return $price;
    }

    /**
     * Get currency
     *
     * @return \XLite\Model\Currency
     */
    public function getCurrency()
    {
        return \XLite::getInstance()->getCurrency();
    }

    /**
     * Get title
     *
     * @return string
     */
    protected function getHead()
    {
        return static::t('SkinActFrequentlyBoughtTogether frequently bought together');
    }

    /**
     * Get page body file
     *
     * @return string
     */
    protected function getPageBodyFile()
    {
        return 'body.twig';
    }

    /**
     * Get a block classes
     *
     * @return string
     */
    protected function getBlockClasses(): string
    {
        return 'block block-block' . $this->getCurrentBlockClass();
    }

    /**
     * Get a current block class
     *
     * @return string
     */
    protected function getCurrentBlockClass(): string
    {
        return ' frequently-bought-together-block';
    }

    /**
     * Postprocess search case
     *
     * @param \XLite\Core\CommonCell $searchCase
     *
     * @return \XLite\Core\CommonCell
     */
    protected function postprocessSearchCase(\XLite\Core\CommonCell $searchCase)
    {
        $searchCase = parent::postprocessSearchCase($searchCase);

        if ($this->isUnsetParentSearchCase()) {
            $this->prepareUnsetSearchCase($searchCase);
        }

        $searchCase->{\Qualiteam\SkinActFrequentlyBoughtTogether\Model\Repo\Product::P_FREQ_BOUGHT_ORDER_ITEMS}
            = $this->getOrderIds($this->getProductId());

        $searchCase->{\XLite\Model\Repo\Product::P_EXCL_PRODUCT_ID} = $this->getProMembershipProductsIds();

        $searchCase->limit = [0, Config::getInstance()->CDev->ProductAdvisor->cbb_max_count_in_block];

        return $searchCase;
    }

    protected function getProMembershipProducts()
    {
        return $this->executeCachedRuntime(
            static function () {
                return \XLite\Core\Database::getRepo(Product::class)->getProMembershipProducts();
            },
            [__CLASS__, self::class, __METHOD__]
        );
    }

    protected function getProMembershipProductsIds(): array
    {
        $items = $this->getProMembershipProducts();
        $result = [];

        foreach ($items as $item) {
            $result[] = $item->getProductId();
        }

        return $result;
    }

    /**
     * Is unset parent search case
     *
     * @return bool
     */
    protected function isUnsetParentSearchCase(): bool
    {
        return true;
    }

    /**
     * Prepare unset parent params search case
     *
     * @param CommonCell $searchCase
     *
     * @return void
     */
    protected function prepareUnsetSearchCase(CommonCell $searchCase): void
    {
        unset($searchCase->{\XLite\Model\Repo\Product::P_EXCL_PRODUCT_ID});
        unset($searchCase->{\CDev\ProductAdvisor\Model\Repo\Product::P_PROFILE_ID});
    }

    protected function getOrderBy()
    {
        return ['p.product_id', 'DESC'];
    }

    protected function isCustomerLogged(): bool
    {
        return Auth::getInstance()->isLogged();
    }
}