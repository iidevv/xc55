<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

use XLite\Core\Database;
use XLite\Core\Request;
use XLite\Core\Session;

/**
 * Adding products to the category.
 */
class CategoryProductSelections extends ProductSelections
{
    /**
     * Check if the product id which will be displayed as "Already added"
     *
     * @param integer $productId Product ID.
     *
     * @return boolean
     */
    public function isExcludedProductId($productId)
    {
        $result             = false;
        $selectedCategoryId = (int)Request::getInstance()->id;
        if ($selectedCategoryId) {
            $searchParams = $this->getSessionCellName();
            Session::getInstance()->$searchParams = array_merge(
                Session::getInstance()->$searchParams,
                ['id' => $selectedCategoryId]
            );
        } else {
            $selectedCategoryId = $this->getCondition('id');
        }
        if ($selectedCategoryId) {
            /** @var \XLite\Model\Product $product */
            $product = Database::getRepo('XLite\Model\Product')->findOneBy(
                ['product_id' => $productId]
            );
            if ($product) {
                $result = array_reduce(
                    $product->getCategories(),
                    static function ($carry, $item) use ($selectedCategoryId) {
                        return ($carry || $item->getCategoryId() === $selectedCategoryId);
                    },
                    $result
                );
            }
        }
        return $result;
    }

    protected function doActionUpdate()
    {
        $id = (int) Request::getInstance()->id;
        $items = (array) Request::getInstance()->select;
        if ($items && $id) {
            /** @var \XLite\Model\Category $category */
            $category = Database::getRepo('XLite\Model\Category')->findOneBy([
                'category_id' => $id
            ]);
            if ($category) {
                $entityManager = Database::getEM();
                foreach ($items as $productId => $value) {
                    if ($value) {
                        /** @var \XLite\Model\Product $product */
                        $product = Database::getRepo('XLite\Model\Product')->findOneBy(
                            ['product_id' => $productId]
                        );
                        $product->addCategory($category);
                        $entityManager->persist($product);
                    }
                }
                $entityManager->flush();
            }
        }
        $this->setHardRedirect();
        $this->setReturnURL($this->buildURL(
            'category_products',
            '',
            empty($id) ? [] : [ 'id' => $id ]
        ));
    }
}
