<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\SimpleCMS\View\ItemsList;

use XLite\Core\Converter;
use XLite\Core\Database;
use XLite\Core\URLManager;
use XLite\Core\Cache\ExecuteCached;
use CDev\Sale\Model\SaleDiscount;

class CustomerLink extends \XLite\View\AView
{
    public const PARAM_ENTITY = 'entity';

    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_ENTITY => new \XLite\Model\WidgetParam\TypeObject('Entity', null)
        ];
    }

    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/SimpleCMS/items_list/model/table/parts/customer_link.twig';
    }

    /**
     * @return \CDev\SimpleCMS\Model\Page
     */
    public function getEntity()
    {
        return $this->getParam(static::PARAM_ENTITY);
    }

    /**
     * @return bool
     */
    public function isOneItemLink()
    {
        return $this->getEntity()->isCategoryPage()
            || $this->getEntity()->isProductPage()
            || $this->getEntity()->isBrandPage();
    }

    /**
     * @return string|null
     */
    protected function getUrl()
    {
        $result = null;
        $entity = $this->getEntity();

        if ($this->getEntity() instanceof SaleDiscount) {
            $result = URLManager::getShopURL($this->getEntity()->getCleanURL());
        } elseif ($this->getEntity()->isCategoryPage() && $this->getFirstCategory()) {
            $result = Converter::buildURL('category', null, [
                'category_id' => $this->getFirstCategory()->getCategoryId()
            ], '');
        } elseif ($this->getEntity()->isProductPage() && $this->getFirstProduct()) {
            $result = Converter::buildURL('product', null, [
                'product_id' => $this->getFirstProduct()->getId()
            ], '');
        } elseif ($this->getEntity()->isBrandPage() && $this->getFirstBrand()) {
            $result = Converter::buildURL('brand', null, [
                'brand_id' => $this->getFirstBrand()->getId()
            ], '');
        } elseif ($entity->getFrontUrl()) {
            $result = $entity->getFrontUrl();
            if ($result === '/') {
                $result = '';//for the front page link on the 'Menus & Pages' page in the admin area
            }
        } else {
            $result = Converter::buildURL('page', null, [
                'id' => $entity->getId()
            ], '');
        }

        return \XLite::getInstance()->getShopURL($result);
    }

    /**
     * @return string|null
     */
    protected function getLink()
    {
        $result = null;

        if ($this->getEntity()->isCategoryPage()) {
            $result = static::t('Category page');
        } elseif ($this->getEntity()->isProductPage()) {
            $result = static::t('Product page');
        } elseif ($this->getEntity()->isBrandPage()) {
            $result = static::t('Brand page');
        } else {
            $result = $this->getUrl();
        }

        return $result;
    }

    /**
     * @return mixed
     */
    protected function getFirstProduct()
    {
        return ExecuteCached::executeCachedRuntime(static function () {
            return Database::getRepo('XLite\Model\Product')->findOneBy([
                'enabled' => true
            ]);
        }, [__CLASS__, __METHOD__]);
    }

    /**
     * @return mixed
     */
    protected function getFirstCategory()
    {
        return ExecuteCached::executeCachedRuntime(static function () {
            return Database::getRepo('XLite\Model\Category')
                ->getRootCategory()
                ->getChildren()
                ->first();
        }, [__CLASS__, __METHOD__]);
    }

    /**
     * @return mixed
     */
    protected function getFirstBrand()
    {
        return ExecuteCached::executeCachedRuntime(static function () {
            return Database::getRepo('QSL\ShopByBrand\Model\Brand')->findOneBy([]);
        }, [__CLASS__, __METHOD__]);
    }

    /**
     * @return bool
     */
    protected function isVisible()
    {
        return $this->isOneItemLink() === false
            || $this->getEntity()->isProductPage() && $this->getFirstProduct()
            || $this->getEntity()->isCategoryPage() && $this->getFirstCategory()
            || $this->getEntity()->isBrandPage() && $this->getFirstBrand();
    }
}
