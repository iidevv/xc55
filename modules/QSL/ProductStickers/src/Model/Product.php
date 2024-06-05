<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductStickers\Model;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;
use XLite\Core\Cache\ExecuteCached;
use XLite\Core\Session;

/**
 * @Extender\Mixin
 */
class Product extends \XLite\Model\Product
{
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany (targetEntity="QSL\ProductStickers\Model\ProductSticker", inversedBy="products")
     * @ORM\JoinTable (name="product_stickers_links",
     *      joinColumns={@ORM\JoinColumn(name="product_id", referencedColumnName="product_id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="sticker_id", referencedColumnName="sticker_id", onDelete="CASCADE")}
     * )
     */
    protected $product_stickers;

    /**
     * @return \XLite\Model\Product
     * @throws \Doctrine\ORM\ORMException
     */
    public function cloneEntity()
    {
        $newProduct = parent::cloneEntity();

        if ($this->getProductStickers()) {
            foreach ($this->getProductStickers() as $sticker) {
                $newSticker = $sticker;
                $newSticker->setProduct($newProduct);
                $newProduct->addProductStickers($newSticker);

                \XLite\Core\Database::getEM()->persist($newSticker);
            }
        }

        return $newProduct;
    }

    /**
     * @param array $data Entity properties OPTIONAL
     *
     * @return void
     */
    public function __construct(array $data = [])
    {
        $this->product_stickers = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * @param \QSL\ProductStickers\Model\ProductSticker $product_stickers
     * @return Product
     */
    public function addProductStickers(\QSL\ProductStickers\Model\ProductSticker $product_stickers)
    {
        $this->product_stickers[] = $product_stickers;
        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProductStickers()
    {
        return $this->product_stickers;
    }

    /**
     * @return array
     */
    public function getPublicProductStickers()
    {
        $cachedData = self::getCachedData() ?: [];
        $languageCode = ($language = Session::getInstance()->getLanguage()) ? $language->getCode() : '';
        $productId = 'product_' . $this->getProductId();

        if (
            isset($cachedData[$productId])
            && isset($cachedData[$productId][$languageCode])
        ) {
            $stickers = $cachedData[$productId][$languageCode];
        } else {
            $stickers = $this->mergeStickerCollection($this->getProductStickers(), $this->getCategoryStickers());
            $stickers = $this->prepareProductStickersData($stickers);
            $cachedData = array_merge_recursive($cachedData, [
                $productId => [$languageCode => $stickers]
            ]);
            ExecuteCached::setCache($this->getCacheKey(), $cachedData, 3600);
        }

        return $stickers;
    }

    /**
     * @return string
     */
    protected static function getCacheKey()
    {
        return ExecuteCached::getCacheKey([__CLASS__, 'getPublicProductStickers']);
    }

    /**
     * @return mixed|null
     */
    protected static function getCachedData()
    {
        return ExecuteCached::getCache(static::getCacheKey()) ?? [];
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $stickers
     *
     * @return array
     */
    protected function prepareProductStickersData(\Doctrine\Common\Collections\Collection $stickers)
    {
        $result = array_map(static function ($v) {
            return [
                'name'       => $v->getName(),
                'enabled'    => $v->getEnabled(),
                'bg_color'   => $v->getBgColor(),
                'text_color' => $v->getTextColor(),
                'position'   => $v->getPosition()
            ];
        }, $stickers->toArray());

        usort($result, static function ($a, $b) {
            if ($a['position'] == $b['position']) {
                return 0;
            }
            return ($a['position'] < $b['position']) ? -1 : 1;
        });

        return $result;
    }

    /**
     * @param \XLite\Model\Product|null $product
     */
    public static function removeProductStickerCache(\XLite\Model\Product $product = null)
    {
        if ($product) {
            $cachedData = self::getCachedData();
            $productId  = 'product_' . $product->getProductId();

            if (isset($cachedData[$productId])) {
                unset($cachedData[$productId]);
                ExecuteCached::setCache(self::getCacheKey(), $cachedData, 3600);
            }
        } else {
            $driver = \XLite\Core\Cache::getInstance()->getDriver();
            $driver->delete(self::getCacheKey());
        }
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    protected function getCategoryStickers()
    {
        $stickers = $this->getCategory()->getCategoryStickers();
        foreach ($this->getCategory()->getPath() as $category) {
            if ($category->isStickersIncludedSubcategories()) {
                $stickers = $this->mergeStickerCollection($category->getCategoryStickers(), $stickers);
            }
        }
        return $stickers;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $collection1
     * @param \Doctrine\Common\Collections\Collection $collection2
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    protected function mergeStickerCollection(
        \Doctrine\Common\Collections\Collection $collection1,
        \Doctrine\Common\Collections\Collection $collection2
    ) {
        $result = new \Doctrine\Common\Collections\ArrayCollection($collection1->toArray());
        $iterator = $collection2->getIterator();
        while ($iterator->key() !== null) {
            if ($result->contains($iterator->current()) === false) {
                $result->add($iterator->current());
            }
            $iterator->next();
        }
        return $result;
    }

    /**
     * @param \QSL\ProductStickers\Model\ProductSticker[] $product_stickers
     */
    public function addProductStickersByProductStickers($product_stickers)
    {
        foreach ($product_stickers as $product_sticker) {
            if (!$this->hasProductStickerByProductSticker($product_sticker)) {
                $this->addProductStickers($product_sticker);
            }
        }
    }

    /**
     * @param \QSL\ProductStickers\Model\ProductSticker[] $product_stickers
     */
    public function removeProductStickersByProductStickers($product_stickers)
    {
        foreach ($product_stickers as $product_sticker) {
            if ($this->hasProductStickerByProductSticker($product_sticker)) {
                $this->getProductStickers()->removeElement($product_sticker);
            }
        }
    }

    /**
     * @param \QSL\ProductStickers\Model\ProductSticker[] $product_stickers
     */
    public function replaceProductStickersByProductStickers($product_stickers)
    {
        $ids = array_map(static function ($item) {
            /** @var \QSL\ProductStickers\Model\ProductSticker $item */
            return (int) $item->getProductStickerId();
        }, $product_stickers);

        $toRemove = [];
        foreach ($this->getProductStickers() as $product_sticker) {
            if (!in_array((int) $product_sticker->getProductStickerId(), $ids, true)) {
                $toRemove[] = $product_sticker;
            }
        }

        $this->addProductStickersByProductStickers($product_stickers);
        $this->removeProductStickersByProductStickers($toRemove);
    }

    /**
     * @param \QSL\ProductStickers\Model\ProductSticker $product_sticker
     *
     * @return boolean
     */
    public function hasProductStickerByProductSticker($product_sticker)
    {
        return (bool) $this->getProductStickerByProductSticker($product_sticker);
    }

    /**
     * @param \QSL\ProductStickers\Model\ProductSticker $product_sticker
     *
     * @return mixed|null
     */
    public function getProductStickerByProductSticker(\QSL\ProductStickers\Model\ProductSticker $product_sticker)
    {
        foreach ($this->getProductStickers() as $product_stickerObject) {
            if ((int) $product_sticker->getProductStickerId() === (int) $product_stickerObject->getProductStickerId()) {
                return $product_stickerObject;
            }
        }

        return null;
    }
}
