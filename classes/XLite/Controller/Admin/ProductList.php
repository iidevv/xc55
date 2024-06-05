<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

use XLite\Controller\Features\SearchByFilterTrait;

/**
 * Products list controller
 */
class ProductList extends \XLite\Controller\Admin\ACL\Catalog
{
    use SearchByFilterTrait;

    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        return array_merge(parent::defineFreeFormIdActions(), ['search']);
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return static::t('Products');
    }

    /**
     * Get itemsList class
     *
     * @return string
     */
    public function getItemsListClass()
    {
        return parent::getItemsListClass() ?: 'XLite\View\ItemsList\Model\Product\Admin\Search';
    }

    protected function doNoAction()
    {
        parent::doNoAction();

        if (\XLite\Core\Request::getInstance()->fast_search) {
            // Clear stored filter within stored search conditions
            \XLite\Core\Session::getInstance()->{$this->getSessionCellName()} = [];

            // Refresh search parameters from the request
            $this->fillSearchValuesStorage();

            // Get ItemsList widget
            $widget = $this->getItemsList();

            // Search for single product entity
            $entity = $widget->searchForSingleEntity();

            if ($entity && $entity instanceof \XLite\Model\Product) {
                // Prepare redirect to product page
                $url = $this->buildURL('product', '', ['product_id' => $entity->getProductId()]);
                $this->setReturnURL($url);
            }
        }
    }

    protected function doActionClone()
    {
        $select = \XLite\Core\Request::getInstance()->select;

        if ($select && is_array($select)) {
            $products = \XLite\Core\Database::getRepo('XLite\Model\Product')->findByIds(array_keys($select));
            if (0 < count($products)) {
                foreach ($products as $product) {
                    $newProduct = $product->cloneEntity();
                    $newProduct->updateQuickData();
                }
                if (1 < count($products)) {
                    $this->setReturnURL($this->buildURL('cloned_products'));
                } else {
                    $this->setReturnURL($this->buildURL('product', '', ['product_id' => $newProduct->getId()]));
                }
            }
        } else {
            \XLite\Core\TopMessage::addWarning('Please select the products first');
        }
    }

    protected function doActionEnable()
    {
        $select = \XLite\Core\Request::getInstance()->select;

        if ($select && is_array($select)) {
            \XLite\Core\Database::getRepo('XLite\Model\Product')->updateInBatchById(
                array_fill_keys(
                    array_keys($select),
                    ['enabled' => true]
                )
            );
            \XLite\Core\TopMessage::addInfo(
                'Products information has been successfully updated'
            );
        } elseif ($ids = $this->getActionProductsIds()) {
            $qb    = \XLite\Core\Database::getRepo('XLite\Model\Product')->createQueryBuilder();
            $alias = $qb->getMainAlias();
            $qb->update('XLite\Model\Product', $alias)
                ->set("{$alias}.enabled", $qb->expr()->literal(true))
                ->andWhere($qb->expr()->in("{$alias}.product_id", $ids))
                ->execute();
            \XLite\Core\TopMessage::addInfo('Products information has been successfully updated');
        } else {
            \XLite\Core\TopMessage::addWarning('Please select the products first');
        }
    }

    protected function doActionDisable()
    {
        $select = \XLite\Core\Request::getInstance()->select;

        if ($select && is_array($select)) {
            \XLite\Core\Database::getRepo('XLite\Model\Product')->updateInBatchById(
                array_fill_keys(
                    array_keys($select),
                    ['enabled' => false]
                )
            );
            \XLite\Core\TopMessage::addInfo(
                'Products information has been successfully updated'
            );
        } elseif ($ids = $this->getActionProductsIds()) {
            $qb    = \XLite\Core\Database::getRepo('XLite\Model\Product')->createQueryBuilder();
            $alias = $qb->getMainAlias();
            $qb->update('XLite\Model\Product', $alias)
                ->set("{$alias}.enabled", $qb->expr()->literal(false))
                ->andWhere($qb->expr()->in("{$alias}.product_id", $ids))
                ->execute();
            \XLite\Core\TopMessage::addInfo('Products information has been successfully updated');
        } else {
            \XLite\Core\TopMessage::addWarning('Please select the products first');
        }
    }

    protected function doActionDelete()
    {
        $select = \XLite\Core\Request::getInstance()->select;

        if ($select && is_array($select)) {
            \XLite\Core\Database::getRepo('XLite\Model\Product')->deleteInBatchById($select);
            \XLite\Core\TopMessage::addInfo('Products information has been successfully deleted');
        } elseif ($ids = $this->getActionProductsIds()) {
            \XLite\Core\Database::getRepo('XLite\Model\Product')->deleteInBatchById(array_flip($ids));
            \XLite\Core\TopMessage::addInfo('Products information has been successfully deleted');
        } else {
            \XLite\Core\TopMessage::addWarning('Please select the products first');
        }
    }

    /**
     * @return array
     */
    protected function getActionProductsIds()
    {
        $cnd = $this->getItemsList()->getActionSearchCondition();
        $ids = \XLite\Core\Database::getRepo('XLite\Model\Product')
            ->search($cnd, \XLite\Model\Repo\ARepo::SEARCH_MODE_IDS);
        $ids = is_array($ids) ? array_unique(array_filter($ids)) : [];

        return $ids;
    }

    protected function doActionSearchItemsList()
    {
        // Clear stored search conditions
        \XLite\Core\Session::getInstance()->{$this->getSessionCellName()} = [];

        parent::doActionSearchItemsList();

        $this->setReturnURL($this->getURL(['mode' => 'search', 'searched' => 1]));
    }

    /**
     * Return search parameters for product list.
     * It is based on search params from Product Items list viewer
     *
     * @return array
     */
    protected function getSearchParams()
    {
        return parent::getSearchParams()
            + $this->getSearchParamsCheckboxes();
    }

    /**
     * Return search parameters for product list given as checkboxes: (0, 1) values
     *
     * @return array
     */
    protected function getSearchParamsCheckboxes()
    {
        $productsSearchParams = [];

        $itemsListClass = $this->getItemsListClass();
        $cBoxFields     = [
            $itemsListClass::PARAM_SEARCH_IN_SUBCATS,
            $itemsListClass::PARAM_BY_TITLE,
            $itemsListClass::PARAM_BY_DESCR,
        ];

        foreach ($cBoxFields as $requestParam) {
            $productsSearchParams[$requestParam] = isset(\XLite\Core\Request::getInstance()->$requestParam) ? 1 : 0;
        }

        return $productsSearchParams;
    }
}
