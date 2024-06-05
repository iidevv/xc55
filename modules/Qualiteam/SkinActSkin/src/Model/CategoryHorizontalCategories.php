<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkin\Model;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;
use XLite\Core\Database;


/**
 * @Extender\Mixin
 * @Extender\Depend("QSL\HorizontalCategoriesMenu")
 */
class CategoryHorizontalCategories extends \XLite\Model\Category
{
    /**
     * Set flyoutColumns
     *
     * @param integer $value
     * @return Category
     */
    public function setFlyoutColumns($value)
    {
        $this->flyoutColumns = $this->isRootCategory() ? 1 : $value;

        return $this;
    }

    /**
     * Get flyoutColumns
     *
     * @return integer
     */
    public function getFlyoutColumns()
    {
        return $this->isRootCategory() ? 1 : parent::getFlyoutColumns();
    }

    public function isRootCategory()
    {
        return Database::getRepo(\XLite\Model\Category::class)->getRootCategoryId() === $this->getParentId();
    }
}
