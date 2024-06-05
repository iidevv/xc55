<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoFeature\View\FormField\Select;

use Qualiteam\SkinActVideoFeature\Model\VideoCategory as VideoCategoryModel;

class VideoCategory extends \XLite\View\FormField\Select\ASelect
{
    public const INDENT_STRING     = '-';
    public const INDENT_MULTIPLIER = 3;

    /**
     * Widget param names
     */
    public const PARAM_DISPLAY_ANY_CATEGORY  = 'displayAnyCategory';
    public const PARAM_DISPLAY_NO_CATEGORY   = 'displayNoCategory';
    public const PARAM_EXCLUDE_CATEGORY      = 'excludeCategory';
    public const PARAM_DISPLAY_ROOT_CATEGORY = 'displayRootCategory';

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_DISPLAY_ANY_CATEGORY => new \XLite\Model\WidgetParam\TypeBool('Display \'Any category\' row', false),
            static::PARAM_DISPLAY_NO_CATEGORY  => new \XLite\Model\WidgetParam\TypeBool('Display \'No category\' row', false),
            static::PARAM_EXCLUDE_CATEGORY => new \XLite\Model\WidgetParam\TypeInt('Excluded category ID', null),
            static::PARAM_DISPLAY_ROOT_CATEGORY => new \XLite\Model\WidgetParam\TypeBool('Display root category', false),
        ];
    }

    /**
     * Return default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [];
    }

    /**
     * getOptions
     *
     * @return array
     */
    protected function getOptions()
    {
        $list = parent::getOptions();

        $list += $this->getCategories();

        if ($this->getParam(static::PARAM_DISPLAY_ROOT_CATEGORY)) {
            $list = [$this->getRootCategoryId() => static::t('SkinActVideoFeature root category')] + $list;
        }

        if ($this->getParam(static::PARAM_DISPLAY_NO_CATEGORY)) {
            $list = ['no_category' => static::t('SkinActVideoFeature no category assigned')] + $list;
        }

        if ($this->getParam(static::PARAM_DISPLAY_ANY_CATEGORY)) {
            $list = [static::t('SkinActVideoFeature any category')] + $list;
        }

        return $list;
    }

    /**
     * Return default options list
     *
     * @return array
     */
    protected function getCategories()
    {
        $list = [];

        $excludeCatId = $this->getParam(static::PARAM_EXCLUDE_CATEGORY);

        foreach (\XLite\Core\Database::getRepo(VideoCategoryModel::class)->getPlanListForTree(null, $excludeCatId) as $category) {
            $name = $this->getCategoryName($category) ?: static::t('SkinActVideoFeature n/a');
            $list[$category['id']] = $this->getIndentationString($category) . $name;
        }

        return $list;
    }

    /**
     * Return indentation string for displaying category depth level
     *
     * @param array $category Category data
     *
     * @return string
     */
    protected function getIndentationString(array $category)
    {
        return str_repeat(static::INDENT_STRING, $category['depth'] * static::INDENT_MULTIPLIER);
    }

    /**
     * Return translated category name
     *
     * :KLUDGE: it's the hack to prevent execution of superflous queries
     *
     * @param array $category Category data
     *
     * @return string
     */
    protected function getCategoryName(array $category)
    {
        $name = null;

        $query = \XLite\Core\Translation::getLanguageQuery(\XLite\Core\Session::getInstance()->getLanguage()->getCode());
        foreach ($query as $code) {
            $data = \Includes\Utils\ArrayManager::searchInArraysArray(
                $category['translations'],
                'code',
                $code
            );
            if (!empty($data)) {
                $name = $data['name'];
                break;
            }
        }

        return $name;
    }
}