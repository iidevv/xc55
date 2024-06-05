<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Mapper;

use XcartGraphqlApi\DTO\CategoryDTO;
use XLite\Core\Cache\ExecuteCached;

class Category
{
    protected static $categoriesToLoadCounts = [];
    protected static $categoriesToLoadNames = [];

    /**
     * @param \XLite\Model\Category $category
     *
     * @return CategoryDTO
     */
    public function mapToDto(\XLite\Model\Category $category)
    {
        $dto = new CategoryDTO();

        $dto->categoryModel = $category;

        $dto->id = $category->getCategoryId();
        $dto->parent_id = $category->getParentId();

        $dto->description = function() use ($category) {
            return $category->getDescription();
        };
        $dto->category_url = function() use ($category) {
            return $this->getCategoryUrl($category);
        };
        $dto->image_url = function() use ($category) {
            return $this->getImageUrl($category);
        };
        $dto->banner_url = function() use ($category) {
            return $this->getBannerUrl($category);
        };

        // TODO Check if its ok
        $dto->subcategories_count = $category->getQuickFlags()
            ? $category->getQuickFlags()->getSubcategoriesCountEnabled()
            : 0;

        $dto->products_count = function() use ($dto) {
            static::$categoriesToLoadCounts[] = $dto->id;
            return new \GraphQL\Deferred(function () use ($dto) {
                return $this->loadBufferedCountsForCategory($dto->id);
            });
        };

        $dto->category_name = function() use ($dto) {
            static::$categoriesToLoadNames[] = $dto->id;
            return new \GraphQL\Deferred(function () use ($dto) {
                return $this->loadBufferedNamesForCategory($dto->id);
            });
        };

        return $dto;
    }

    /**
     * Get category URL for JSON API
     *
     * @param \XLite\Model\Category $category
     *
     * @return string
     */
    protected function getCategoryUrl(\XLite\Model\Category $category)
    {
        return \XLite\Core\Converter::buildFullURL(
            'category',
            '',
            [ 'category_id' => $category->getCategoryId() ],
            \XLite::getCustomerScript()
        );
    }


    /**
     * Get category image URL
     *
     * @param \XLite\Model\Category $category
     *
     * @return string
     */
    public function getImageUrl(\XLite\Model\Category $category)
    {
        return $this->getResizedCategoryImageUrl(
            $category->getImage(),
            320
        );
    }

    /**
     * Get category banner URL
     *
     * @param \XLite\Model\Category $category
     *
     * @return string
     */
    public function getBannerUrl(\XLite\Model\Category $category)
    {
        return $category->getBanner()
            ? $category->getBanner()->getFrontURL()
            : '';
    }

    /**
     * Get resized product image URL for JSON API
     *
     * @param \XLite\Model\Base\Image $image Image
     * @param integer                 $size  Image size OPTIONAL
     *
     * @return string
     */
    protected function getResizedCategoryImageUrl($image, $size = 0)
    {
        $url = '';

        if ($image) {
            if ($size > 0) {
                $resizedData = $image->getResizedURL($size, $size);

                $url = $resizedData[2];
            } else {
                $url = $image->getFrontURL();
            }
        }

        return $url;
    }

    protected function loadBufferedCountsForCategory($id)
    {
        $buffed = ExecuteCached::executeCachedRuntime(
            function() {
                return \XLite\Core\Database::getRepo('XLite\Model\Category')
                    ->getProductsCountsForMobileApi(static::$categoriesToLoadCounts);
            }, [
                'mobile_api-loadBufferedCountsForCategory'
            ]
        );

        return isset($buffed[$id])
            ? $buffed[$id]['countValue']
            : 0;
    }

    protected function loadBufferedNamesForCategory($id)
    {
        $buffed = ExecuteCached::executeCachedRuntime(
            function() {
                return \XLite\Core\Database::getRepo('XLite\Model\Category')
                    ->getNamesForMobileApi(static::$categoriesToLoadNames);
            }, [
                'mobile_api-loadBufferedNamesForCategory'
            ]
        );

        return isset($buffed[$id])
            ? $buffed[$id]['name']
            : '';
    }
}
