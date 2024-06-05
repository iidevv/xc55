<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActImagesForColors\View\ItemsList\Model;


class ImagesForColors extends \XLite\View\ItemsList\Model\Table
{

    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            'product_id' => new \XLite\Model\WidgetParam\TypeInt('product_id'),
        );
    }

    public static function getSearchParams()
    {
        return [
            'product_id' => 'product_id',
        ];
    }

    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        foreach (static::getSearchParams() as $modelParam => $requestParam) {
            $paramValue = $this->getParam($requestParam);

            if ($paramValue !== '' && $paramValue !== 0) {
                $result->$modelParam = $paramValue;
            }
        }

        return $result;
    }

    protected function defineRepositoryName()
    {
        return '\XLite\Model\Image\Product\Image';
    }

    protected function defineColumns()
    {
        return [
            'img' => [
                static::COLUMN_NAME => static::t('SkinActImagesForColors image'),
                static::COLUMN_TEMPLATE => 'modules/Qualiteam/SkinActImagesForColors/image.twig',
                static::COLUMN_ORDERBY => 100,
            ],
            'swatch' => [
                static::COLUMN_NAME => static::t('SkinActImagesForColors swatch'),
                static::COLUMN_CLASS => '\Qualiteam\SkinActImagesForColors\View\FormField\Select\SwatchInline',
                static::COLUMN_ORDERBY => 200,
            ],
        ];

    }


    protected function isMainColumn(array $column)
    {
        return null;
    }


}
