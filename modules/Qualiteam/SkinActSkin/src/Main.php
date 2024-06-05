<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkin;

use XCart\Event\Service\ViewListMutationEvent;
use XLite\Core\Layout;
use XLite\Core\Database;
use XLite\Model\Cart;
use XLite\Model\Category;
use XLite\Model\OrderItem;

abstract class Main extends \XLite\Module\AModuleSkin
{
    /**
     * Check if skin is based on Crisp White theme
     *
     * @return boolean
     */
    public static function isCrispWhiteBasedSkin()
    {
        return true;
    }

    /**
     * Returns supported layout types
     *
     * @return array
     */
    public static function getLayoutTypes()
    {
        $types = parent::getLayoutTypes();
        $types[Layout::LAYOUT_GROUP_HOME]    = [Layout::LAYOUT_ONE_COLUMN];
        $types[Layout::LAYOUT_GROUP_DEFAULT] = [
            Layout::LAYOUT_ONE_COLUMN,
            Layout::LAYOUT_TWO_COLUMNS_LEFT
        ];

        return $types;
    }

    public static function convertAttributesToHTMLString(array $attributes): string
    {
        $result = [];
        array_walk(
            $attributes,
            static function ($value, string $attr) use (&$result): void {
                if ($value === null) {
                    $result[] = htmlspecialchars($attr);
                } else {
                    $result[] = htmlspecialchars($attr) . '="' . htmlspecialchars(strval($value)) . '"';
                }
            }
        );

        return implode(' ', $result);
    }

    public static function isStoreHasCategories(): bool
    {
        $rootCategory = Database::getRepo(Category::class)->getRootCategory();

        return $rootCategory && $rootCategory->hasSubcategories();
    }

    public static function getProductsCountForBadge(): string
    {
        $productsAdded = array_reduce(
            Cart::getInstance()->getItems()->toArray(),
            static fn(int $carry, OrderItem $item): int => $carry + $item->getAmount(),
            0
        );

        if ($productsAdded === 0) {
            return '';
        }

        return ($productsAdded <= 9) ? (string)$productsAdded : '9+';
    }
}
