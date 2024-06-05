<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductStickers\View\FormField\Select;

class CategoryStickers extends \QSL\ProductStickers\View\FormField\Select\ProductStickers
{
    public const PARAM_INCLUDE_SUBCATEGORIES = 'is_stickers_included_subcategories';

    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_INCLUDE_SUBCATEGORIES => new \XLite\Model\WidgetParam\TypeBool('', false)
        ];
    }

    /**
     * @return string
     */
    public function getIsStickersIncludedSubcategoriesWidget()
    {
        $widget = $this->getChildWidget('XLite\View\FormField\Input\Checkbox', [
            'label' => 'Include subcategories',
            'fieldName' => $this->getNamePostedData('is_stickers_included_subcategories'),
            'isChecked' => $this->getParam(self::PARAM_INCLUDE_SUBCATEGORIES)
        ]);

        return $widget->getContent();
    }

    /**
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'modules/QSL/ProductStickers/form_field/category_stickers/select.twig';
    }

    /**
     * @return string
     */
    protected function getDir()
    {
        return '';
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = '/modules/QSL/ProductStickers/form_field/category_stickers/style.css';
        ;

        return $list;
    }
}
