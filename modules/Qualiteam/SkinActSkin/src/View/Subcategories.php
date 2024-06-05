<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkin\View;

use Doctrine\Common\Collections\ArrayCollection;
use XLite\Core\Cache\ExecuteCachedTrait;
use XCart\Extender\Mapping\Extender;

/**
 * Subcategories list
 *
 * @Extender\Mixin
 */
class Subcategories extends \XLite\View\Subcategories
{
    use ExecuteCachedTrait;

    /**
     * Return subcategories
     *
     * @return ArrayCollection
     */
    protected function getSubcategories()
    {
        return \XLite\Core\Request::getInstance()->target === 'main'
            ? $this->getHomeCategoriesList()
            : parent::getSubcategories();
    }

    /**
     * Check for subcategories
     *
     * @return boolean
     */
    protected function hasSubcategories()
    {
        return \XLite\Core\Request::getInstance()->target === 'main'
            ? $this->getHomeCategoriesList()
            : parent::hasSubcategories();
    }

    /**
     * Get Home page categories condifion
     * @return \XLite\Core\CommonCell
     */
    protected function getHomeCategoriesCondition()
    {
        $cnd = new \XLite\Core\CommonCell();
        $cnd->{\XLite\Model\Repo\Category::P_SHOW_ON_HOME_PAGE} = 1;

        return $cnd;
    }

    /**
     * Return subcategories marked as Show in Shop by Category
     *
     * @return ArrayCollection
     */
    protected function getHomeCategoriesList() {
        return $this->executeCachedRuntime(function () {
            $cnd = $this->getHomeCategoriesCondition();
            $shopByCategories = \XLite\Core\Database::getRepo('XLite\Model\Category')->search($cnd);

            return $shopByCategories;
        }, [__METHOD__, $this->shopByCategories, self::class]);
    }

    protected function needAnimation() {
        return (\XLite\Core\Request::getInstance()->target === 'main');
    }

}
