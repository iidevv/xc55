<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFrequentlyBoughtTogether\Module\XC\BulkEditing\Logic\BulkEdit;

use Qualiteam\SkinActFrequentlyBoughtTogether\Main;
use Qualiteam\SkinActFrequentlyBoughtTogether\Traits\FreqBoughtTogetherTrait;
use XCart\Extender\Mapping\Extender;
use Qualiteam\SkinActFrequentlyBoughtTogether\Module\XC\BulkEditing\Logic\BulkEdit\Field\Category\FrequentlyBoughtTogether;
use XLite\Core\Translation;

/**
 * @Extender\Mixin
 * @Extender\Depend ("XC\BulkEditing")
 */
class FrequentlyBoughtTogetherScenario extends \XC\BulkEditing\Logic\BulkEdit\Scenario
{
    use FreqBoughtTogetherTrait;

    const PRODUCT_CATEGORIES_SCENARIO_NAME = 'product_categories';
    const SCENARIO_DEFAULT_TITLE_NAME      = 'title';
    const SCENARIO_DEFAULT_FIELDS_NAME     = 'fields';
    const SCENARIO_DEFAULT_CHILDREN_NAME   = 'default';

    protected static function defineScenario()
    {
        $result                      = parent::defineScenario();
        $scenarioTitle               = static::getCustomScenarioTitleName() ?: static::getDefaultScenarioTitleName();
        $scenarioName                = static::getCustomScenarioName() ?: static::getDefaultScenarioName();
        $scenarioFieldsName          = static::getCustomScenarioFieldsName() ?: static::getDefaultScenarioFieldsName();
        $scenarioChildrenName        = static::getCustomScenarioChildrenName() ?: static::getDefaultScenarioChildrenName();
        $scenarioChildrenSectionName = static::getFreqBoughtTogetherFieldName();

        if (static::isCorrectingLabelWithModuleDepend()) {
            $result[$scenarioName][$scenarioTitle] = Main::isModuleEnabled('XC-ProductTags')
                ? Translation::getInstance()->translate('SkinActFrequentlyBoughtTogether category and tags and hide flags')
                : Translation::getInstance()->translate('SkinActFrequentlyBoughtTogether category and hide flags');
        }

        $result[$scenarioName][$scenarioFieldsName][$scenarioChildrenName][$scenarioChildrenSectionName] = [
            'class'   => static::getFreqBoughtTogetherFieldClassName(),
            'options' => [
                'position' => static::getFreqBoughtTogetherFieldPosition(),
            ],
        ];

        return $result;
    }

    /**
     * @return string
     */
    protected static function getCustomScenarioTitleName(): string
    {
        return '';
    }

    protected static function getDefaultScenarioTitleName(): string
    {
        return static::SCENARIO_DEFAULT_TITLE_NAME;
    }

    /**
     * @return string
     */
    protected static function getCustomScenarioName(): string
    {
        return '';
    }

    protected static function getDefaultScenarioName(): string
    {
        return static::PRODUCT_CATEGORIES_SCENARIO_NAME;
    }

    /**
     * @return string
     */
    protected static function getCustomScenarioFieldsName(): string
    {
        return '';
    }

    protected static function getDefaultScenarioFieldsName(): string
    {
        return static::SCENARIO_DEFAULT_FIELDS_NAME;
    }

    /**
     * @return string
     */
    protected static function getCustomScenarioChildrenName(): string
    {
        return '';
    }

    protected static function getDefaultScenarioChildrenName(): string
    {
        return static::SCENARIO_DEFAULT_CHILDREN_NAME;
    }

    protected static function getFreqBoughtTogetherFieldName(): string
    {
        return (new FrequentlyBoughtTogetherScenario)->getExcludeFreqBoughtTogetherParamName();
    }

    protected static function isCorrectingLabelWithModuleDepend(): bool
    {
        return true;
    }

    protected static function getFreqBoughtTogetherFieldClassName(): string
    {
        return FrequentlyBoughtTogether::class;
    }

    protected static function getFreqBoughtTogetherFieldPosition(): int
    {
        return (new FrequentlyBoughtTogetherScenario)->getExcludeFreqBoughtTogetherParamPosition();
    }
}