<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\HorizontalCategoriesMenu\Model;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;

/**
 * Category
 * @Extender\Mixin
 */
class Category extends \XLite\Model\Category
{
    /**
     * Number of columns for subcategories in the flyout menu
     *
     * @var integer
     *
     * @ORM\Column (type="integer", length=11, nullable=false)
     */
    protected $flyoutColumns = 0;

    /**
     * Public morozov for protected cleanDTOsCache()
     *
     * @return  void
     */
    public function publicCleanDTOsCache()
    {
        $this->cleanDTOsCache();
    }

    /**
     * Set flyoutColumns
     *
     * @param integer $value
     * @return Category
     */
    public function setFlyoutColumns($value)
    {
        $this->flyoutColumns = $value;
        return $this;
    }

    /**
     * Get flyoutColumns
     *
     * @return integer
     */
    public function getFlyoutColumns()
    {
        return $this->flyoutColumns == 0
            ? \XLite\Core\Config::getInstance()->QSL->HorizontalCategoriesMenu->hfcm_default_columns_count
            : $this->flyoutColumns;
    }
}
