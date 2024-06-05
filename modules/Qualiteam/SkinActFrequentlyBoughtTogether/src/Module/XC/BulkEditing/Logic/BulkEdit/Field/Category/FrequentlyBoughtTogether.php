<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFrequentlyBoughtTogether\Module\XC\BulkEditing\Logic\BulkEdit\Field\Category;

use Qualiteam\SkinActFrequentlyBoughtTogether\Traits\FreqBoughtTogetherTrait;

/**
 * @Extender\Depend ("XC\BulkEditing")
 */
class FrequentlyBoughtTogether extends \XC\BulkEditing\Logic\BulkEdit\Field\AField
{
    use FreqBoughtTogetherTrait;

    public static function getSchema($name, $options)
    {
        return [
            $name => [
                'label'    => static::getFreqBoughtTogetherFieldLabel(),
                'type'     => static::getFreqBoughtTogetherFieldType(),
                'position' => static::getFreqBoughtTogetherFieldPosition(),
            ],
        ];
    }

    protected static function getFreqBoughtTogetherFieldLabel(): string
    {
        return (new FrequentlyBoughtTogether)->getExcludeFreqBoughtTogetherParamLabel();
    }

    protected static function getFreqBoughtTogetherFieldType(): string
    {
        return (new FrequentlyBoughtTogether)->getExcludeFreqBoughtTogetherParamInputType();
    }

    protected static function getFreqBoughtTogetherFieldPosition(): int
    {
        return (new FrequentlyBoughtTogether)->getExcludeFreqBoughtTogetherParamPosition();
    }

    public static function populateData($name, $object, $data)
    {
        $object->setExcludeFreqBoughtTogether($data->{$name});
    }

    public static function getViewColumns($name, $options)
    {
        return [
            $name => [
                'name'    => static::t('SkinActFrequentlyBoughtTogether exclude from frequently bought together'),
                'orderBy' => $options['position'] ?? 0,
            ],
        ];
    }

    public static function getViewValue($name, $object)
    {
        return $object->getExcludeFreqBoughtTogether() ? static::t('Yes') : static::t('No');
    }
}