<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCategoryDescriptionAndFaq\View\ItemsList\Model;

/**
 * Category Questions items list
 */
class CategoryQuestions extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), ['category_questions']);
    }

    /**
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'question' => [
                static::COLUMN_NAME         => static::t('SkinActCategoryDescriptionAndFaq Question'),
                static::COLUMN_CREATE_CLASS => 'XLite\View\FormField\Inline\Input\Text',
                static::COLUMN_PARAMS       => ['required' => false],
                static::COLUMN_ORDERBY      => 200,
                static::COLUMN_MAIN         => true,
                static::COLUMN_LINK         => 'category_question'
            ],
        ];
    }

    /**
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'Qualiteam\SkinActCategoryDescriptionAndFaq\Model\CategoryQuestion';
    }

    /**
     * Description for blank items list
     *
     * @return string
     */
    protected function getEmptyListDescription()
    {
        return static::t('SkinActCategoryDescriptionAndFaq empty item list');
    }

    /**
     * @return string
     */
    protected function getCreateButtonLabel()
    {
        return 'Add new';
    }

    /**
     * @return boolean
     */
    protected function isSwitchable()
    {
        return true;
    }

    /**
     * @return integer
     */
    protected function getSortableType()
    {
        return static::SORT_TYPE_INPUT;
    }

    /**
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' category-questions';
    }

    /**
     * @return string|\XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return 'Qualiteam\SkinActCategoryDescriptionAndFaq\View\StickyPanel\ItemsList\CategoryQuestion';
    }

    protected function isRemoved()
    {
        return true;
    }

    /**
     * @return integer
     */
    protected function isInlineCreation()
    {
        return static::CREATE_INLINE_TOP;
    }

    /**
     * @return array
     */
    public static function getSearchParams()
    {
        return [];
    }

    /**
     * Get create entity URL
     *
     * @return string
     */
    protected function getCreateURL()
    {
        return \XLite\Core\Converter::buildURL('category_questions');
    }

    protected function wrapWithFormByDefault()
    {
        return true;
    }

    protected function getFormTarget()
    {
        return 'category_questions';
    }

    /**
     * Insert new entity
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return void
     */
    protected function insertNewEntity(\XLite\Model\AEntity $entity)
    {
        $pos = 10;
        $entity->setPosition($pos);
        parent::insertNewEntity($entity);
    }

    /**
     * Get wrapper form params
     *
     * @return array
     */
    protected function getFormParams()
    {
        $params = [];

        $categoryQuestionId = \XLite\Core\Request::getInstance()->id;
        if ($categoryQuestionId) {
            $params['id'] = $categoryQuestionId;
        }

        return array_merge(parent::getFormParams(), $params);
    }
}
