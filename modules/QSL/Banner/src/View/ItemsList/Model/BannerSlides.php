<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Banner\View\ItemsList\Model;

/**
 * Banner page view
 */
class BannerSlides extends \XLite\View\ItemsList\Model\Table
{
    /**
     * @return array
     */
    protected function defineColumns()
    {
        $columns = [];

        $columns['image'] = [
            static::COLUMN_NAME     => static::t('Image'),
            static::COLUMN_CLASS    => 'XLite\View\FormField\Inline\FileUploader\Image',
            static::COLUMN_ORDERBY  => 100,
        ];

        $columns['actionButton'] = [
            static::COLUMN_NAME     => static::t('Action button'),
            static::COLUMN_CLASS    => 'XLite\View\FormField\Inline\Input\Text',
            static::COLUMN_ORDERBY  => 150,
        ];

        $columns['link'] = [
            static::COLUMN_NAME     => static::t('Link'),
            static::COLUMN_CLASS    => 'XLite\View\FormField\Inline\Input\Text',
            static::COLUMN_MAIN     => true,
            static::COLUMN_ORDERBY  => 200,
        ];

        $columns['maintext'] = [
            static::COLUMN_NAME     => static::t('Main text'),
            static::COLUMN_CLASS    => 'XLite\View\FormField\Inline\Textarea\Simple',
            static::COLUMN_MAIN     => true,
            static::COLUMN_ORDERBY  => 300,
            static::COLUMN_PARAMS   => [
                'maxlength' => '255',
            ],
        ];

        $columns['maintext_color'] = [
            static::COLUMN_CLASS    => 'XLite\View\FormField\Inline\Input\Text\Color',
            static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Main text color'),
            static::COLUMN_MAIN     => true,
            static::COLUMN_ORDERBY  => 400,
        ];

        $columns['addtext'] = [
            static::COLUMN_NAME     => static::t('Additional text'),
            static::COLUMN_CLASS    => 'XLite\View\FormField\Inline\Textarea\Simple',
            static::COLUMN_MAIN     => true,
            static::COLUMN_ORDERBY  => 500,
            static::COLUMN_PARAMS   => [
                'maxlength' => '255',
            ],
        ];
        $columns['addtext_color'] = [
            static::COLUMN_CLASS    => 'XLite\View\FormField\Inline\Input\Text\Color',
            static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Additional text color'),
            static::COLUMN_MAIN     => true,
            static::COLUMN_ORDERBY  => 600,
        ];

        if ($this->getBanner()->getParallax()) {
            $columns['parallaxImage'] = [
                static::COLUMN_NAME      => static::t('Use image for parallax'),
                static::COLUMN_CLASS     => 'XLite\View\FormField\Inline\Input\Radio\Radio',
                static::COLUMN_EDIT_ONLY => true,
                static::COLUMN_PARAMS    => [
                    'fieldName' => 'parallaxImage',
                ],
                static::COLUMN_ORDERBY      => 700,
            ];
        }

        return $columns;
    }

    /**
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'QSL\Banner\Model\BannerSlide';
    }

    /**
     * @return array
     */
    protected function getCommonParams()
    {
        $this->commonParams = parent::getCommonParams();
        $this->commonParams['id']   = $this->getBanner()->getId();
        $this->commonParams['page'] = 'images';

        return $this->commonParams;
    }

    /**
     * @return array
     */
    protected function getFormParams()
    {
        return array_merge(
            parent::getFormParams(),
            [
                'id' => $this->getBanner()->getId(),
                'page' => 'images',
            ]
        );
    }

    /**
     * TODO refactor
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        foreach (static::getSearchParams() as $modelParam => $requestParam) {
            $paramValue = $this->getParam($requestParam);

            if ($paramValue !== '' && $paramValue !== 0) {
                $result->$modelParam = $paramValue;
            }
        }

        $result->banner = $this->getBanner();

        return $result;
    }

    /**
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return void
     */
    protected function insertNewEntity(\XLite\Model\AEntity $entity)
    {
        // Resort
        $entity->setBanner($this->getBanner());

        parent::insertNewEntity($entity);
    }

    /**
     * @return boolean
     */
    protected function isSwitchable()
    {
        return true;
    }

    /**
     * @return boolean
     */
    protected function isRemoved()
    {
        return true;
    }

    /**
     * @return integer
     */
    protected function getSortableType()
    {
        return static::SORT_TYPE_MOVE;
    }

    /**
     * @return integer
     */
    protected function isCreation()
    {
        return static::CREATE_INLINE_TOP;
    }

    /**
     * @return integer
     */
    protected function isInlineCreation()
    {
        return static::CREATE_INLINE_TOP;
    }

    /**
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' banner-system-images';
    }
}
