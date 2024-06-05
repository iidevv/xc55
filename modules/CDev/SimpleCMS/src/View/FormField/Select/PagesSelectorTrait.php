<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\SimpleCMS\View\FormField\Select;

use CDev\SimpleCMS\Model\Menu;
use XLite\Core\Cache\ExecuteCached;
use XLite\Core\Database;

trait PagesSelectorTrait
{
    public static $PATH_SEPARATOR = ' / ';

    /**
     * @return array
     */
    public static function getAllPages()
    {
        return ExecuteCached::executeCachedRuntime(static function () {
            return array_merge(
                static::getDefaultPages(),
                static::getCategoriesPages(),
                static::getPromotionalPages(),
                static::getCustomPages()
            );
        }, [__CLASS__, __METHOD__]);
    }

    /**
     * @return array
     */
    protected static function defineDefaultPages()
    {
        return [
            Menu::DEFAULT_HOME_PAGE  => static::t('Front page'),
            Menu::DEFAULT_MY_ACCOUNT => static::t('My account'),
        ];
    }

    /**
     * @return array
     */
    protected static function getDefaultPages()
    {
        return static::defineDefaultPages();
    }

    /**
     * @return array
     */
    protected static function defineCategoriesMainPage()
    {
        return [
            Menu::DEFAULT_CATEGORIES_PAGE => static::t('Categories')
        ];
    }

    /**
     * @return array
     */
    protected static function defineCategoriesPages()
    {
        $list       = [];
        $categories = Database::getRepo('XLite\Model\Category')->getAllCategoriesAsDTO();

        foreach ($categories as $category) {
            $list['?target=category&category_id=' . $category['id']] = $category['fullName'];
        }

        return $list;
    }

    /**
     * @return array
     */
    protected static function getCategoriesPages()
    {
        return array_merge(
            static::defineCategoriesMainPage(),
            static::addPrefixToPagesList(
                static::t('Categories'),
                static::defineCategoriesPages()
            )
        );
    }

    /**
     * @return array
     */
    protected static function definePromotionalPages()
    {
        return [];
    }

    /**
     * @return array
     */
    protected static function getPromotionalPages()
    {
        return static::addPrefixToPagesList(
            static::t('Promotional page'),
            static::definePromotionalPages()
        );
    }

    /**
     * @return array
     */
    protected static function defineCustomPagesMainPage()
    {
        return [
            Menu::DEFAULT_PAGES => static::t('Custom HTML content pages')
        ];
    }

    /**
     * @return array
     */
    protected static function defineCustomPages()
    {
        $list  = [];
        $pages = Database::getRepo('CDev\SimpleCMS\Model\Page')->findAll();

        /** @var \CDev\SimpleCMS\Model\Page $page */
        foreach ($pages as $page) {
            if (!$page->isPrimaryPage()) {
                $list['?target=page&id=' . $page->getId()] = static::t($page->getName());
            }
        }

        return $list;
    }

    /**
     * @return array
     */
    protected static function getCustomPages()
    {
        return array_merge(
            static::defineCustomPagesMainPage(),
            static::addPrefixToPagesList(
                static::t('Custom HTML content pages'),
                static::defineCustomPages()
            )
        );
    }

    /**
     * @param string $prefix
     * @param array $list
     *
     * @return array
     */
    public static function addPrefixToPagesList($prefix, $list)
    {
        return array_map(static function ($v) use ($prefix) {
            return (string) $prefix . static::$PATH_SEPARATOR . $v;
        }, $list);
    }

    /**
     * @param $path
     * @param $separator
     *
     * @return array
     */
    protected static function stringToArray($path, $separator = ' / ')
    {
        $path = (string) $path;
        $pos  = strpos($path, $separator);

        if ($pos === false) {
            return [$path => null];
        }

        $key  = substr($path, 0, $pos);
        $path = substr($path, $pos + strlen($separator));

        $result = [
            $key => static::stringToArray($path)
        ];

        return $result;
    }

    /**
     * @link https://www.php.net/manual/ru/function.array-merge-recursive.php#92195
     *
     * @param array $array1
     * @param array $array2
     *
     * @return array
     */
    protected static function arrayMergeRecursive(array &$array1, array &$array2)
    {
        $merged = $array1;

        foreach ($array2 as $key => &$value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = static::arrayMergeRecursive($merged [$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }

        return $merged;
    }

    /**
     * @return array
     */
    public static function getPagesMap()
    {
        return ExecuteCached::executeCachedRuntime(static function () {
            $result = [];
            $pages  = static::getAllPages();

            foreach ($pages as $page) {
                $array  = static::stringToArray($page, static::$PATH_SEPARATOR);
                $result = static::arrayMergeRecursive($result, $array);
            }

            return $result;
        }, [__CLASS__, __METHOD__]);
    }

    /**
     * @param string $path
     *
     * @return int
     */
    public static function getChildrenQuantityByPath($path = '')
    {
        $parts = explode(static::$PATH_SEPARATOR, $path);
        $map   = static::getPagesMap();

        while ($part = array_shift($parts)) {
            $map = $map[$part] ?? null;
        }

        return $map ? count($map) : 0;
    }
}
