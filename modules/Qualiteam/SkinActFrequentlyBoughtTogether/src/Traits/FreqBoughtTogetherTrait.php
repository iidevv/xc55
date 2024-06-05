<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFrequentlyBoughtTogether\Traits;

use XLite\View\FormModel\Type\SwitcherType;
use XLite\Core\Translation;
use XLite\Core\Cache\ExecuteCachedTrait;
use XLite\Model\Product;

trait FreqBoughtTogetherTrait
{
    use ExecuteCachedTrait;

    /**
     * Get exclude default frequently bought together name
     *
     * @return string
     */
    protected function getExcludeFreqBoughtTogetherParamName(): string
    {
        return 'isExcludeFreqBoughtTogether';
    }

    /**
     * Get default exclude frequently bought together label
     *
     * @return string
     */
    protected function getExcludeFreqBoughtTogetherParamLabel(): string
    {
        return Translation::getInstance()->translate('SkinActFrequentlyBoughtTogether exclude from frequently bought together');
    }

    /**
     * Get dependent position step
     *
     * @return int
     */
    protected function getExcludeFreqBoughtTogetherParamDependPositionStep(): int
    {
        return 5;
    }

    /**
     * Get default exclude frequently bought together section type
     *
     * @return string
     */
    protected function getExcludeFreqBoughtTogetherParamInputType(): string
    {
        return SwitcherType::class;
    }

    /**
     * Get default exclude frequently bought together position
     *
     * @return int
     */
    protected function getExcludeFreqBoughtTogetherParamPosition(): int
    {
        return 10;
    }

    /**
     * Get default module path
     *
     * @return string
     */
    protected function getModulePath(): string
    {
        return 'modules/Qualiteam/SkinActFrequentlyBoughtTogether';
    }

    protected function getMaxItemsInBlockCount(): int
    {
        return 5;
    }
    
    /**
     * Correcting a product array where current product must be shown first
     *
     * @param array $data
     *
     * @return array
     */
    protected function correctFreqBoughtTogetherProductsPosition(array $data, int $productId): array
    {
        $isCorrected = false;

        foreach ($data as $key => $item) {
            if ($item->getProductId() === $productId) {
                unset($data[$key]);
                array_unshift($data, $item);
                $isCorrected = true;
            }
        }

        if (!$isCorrected) {
            $lastArrayElement = count($data) - 1;
            $product          = $this->getProductFromDB($productId);

            if (!$product->getExcludeFreqBoughtTogether() && $product->getAmount()) {
                unset($data[$lastArrayElement]);
                array_unshift($data, $product);
            }
        }

        return $data;
    }

    /**
     * Correcting a product array where check excluded products
     *
     * @param array $data
     *
     * @return array
     */
    protected function correctExcludeFreqBoughtTogether(array $data): array
    {
        return array_filter($data, function($item) {
            return !$item->getExcludeFreqBoughtTogether();
        });
    }

    /**
     * Get a current product info from the database
     *
     * @param int $productId
     *
     * @return mixed
     */
    private function getProductFromDB(int $productId)
    {
        return $this->executeCachedRuntime(
            static function () use ($productId) {
                return \XLite\Core\Database::getRepo(Product::class)->findOneBy(['product_id' => $productId]);
            },
            [__CLASS__, self::class, __METHOD__, $productId]
        );
    }
}