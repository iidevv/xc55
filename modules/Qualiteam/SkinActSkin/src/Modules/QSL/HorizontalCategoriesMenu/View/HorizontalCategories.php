<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkin\Modules\QSL\HorizontalCategoriesMenu\View;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Database;

/**
 * Categories list
 *
 * @Extender\Mixin
 * @Extender\Depend("QSL\HorizontalCategoriesMenu")
 */
class HorizontalCategories extends \QSL\HorizontalCategoriesMenu\View\HorizontalCategories
{

    /**
     * Assemble item CSS class name
     *
     * @param integer               $index    Item number
     * @param integer               $count    Items count
     * @param array                 $category Current category
     *
     * @return string
     */
    protected function assembleItemClassName($index, $count, $category)
    {
        $classes = parent::assembleItemClassName($index, $count, $category);

        $classes .= " top-main-menu__item top-main-menu__item--color--" . $this->getColorName($category["color"]);

        return $classes;
    }

    public function getColorName($color)
    {
        switch ($color) {
            case "Y":
                return "yellow";
            default:
                return "white";
        }
    }

    /**
     * Preprocess DTO
     *
     * @param  array    $categoryDTO
     * @return array
     */
    protected function preprocessDTO($categoryDTO)
    {

        $categoryDTO = parent::preprocessDTO($categoryDTO);

        $isRootCategory = Database::getRepo('XLite\Model\Category')->getRootCategoryId() == $categoryDTO['parent_id'];

        if ($isRootCategory) {
            $categoryDTO['flyoutColumns'] = 1;
        }

        return $categoryDTO;
    }

}
