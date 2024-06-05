<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductTags\View\ItemsList\Model;

/**
 * Tag items list
 */
class Tag extends \XLite\View\ItemsList\Model\Table
{
    public const PARAM_SEARCH_NAME = 'translations.name';

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'name'     => [
                static::COLUMN_NAME    => \XLite\Core\Translation::lbl('Name'),
                static::COLUMN_CLASS   => 'XC\ProductTags\View\FormField\Inline\Input\Text',
                static::COLUMN_MAIN    => true,
                static::COLUMN_PARAMS  => [
                    \XLite\View\FormField\Input\Base\StringInput::PARAM_REQUIRED => true,
                    \XLite\View\FormField\Input\Base\StringInput::PARAM_MAX_LENGTH => 128,
                ],
                static::COLUMN_ORDERBY => 100,
            ],
            'products' => [
                static::COLUMN_NAME    => static::t('Products'),
                static::COLUMN_ORDERBY => 200,
                static::COLUMN_LINK    => 'product_list',
            ],
        ];
    }

    /**
     * Return search parameters.
     *
     * @return array
     */
    public static function getSearchParams()
    {
        return [
            \XC\ProductTags\Model\Repo\Tag::SEARCH_NAME => static::PARAM_SEARCH_NAME,
        ];
    }

    /**
     * Define so called "request" parameters
     *
     * @return void
     */
    protected function defineRequestParams()
    {
        parent::defineRequestParams();

        $this->requestParams = array_merge($this->requestParams, static::getSearchParams());
    }

    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        foreach (static::getSearchParams() as $modelParam => $requestParam) {
            $paramValue = $this->getParam($requestParam);

            if (is_string($paramValue)) {
                $paramValue = trim($paramValue);
            }

            if ($paramValue !== '') {
                $result->$modelParam = $paramValue;
            }
        }

        $result->{\XC\ProductTags\Model\Repo\Tag::P_ORDER_BY} = $this->getOrderBy();

        return $result;
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return \XC\ProductTags\Model\Tag::class;
    }

    /**
     * Build entity page URL
     *
     * @param \XLite\Model\AEntity $entity Entity
     * @param array                $column Column data
     *
     * @return string
     */
    protected function buildEntityURL(\XLite\Model\AEntity $entity, array $column)
    {
        if ($column[static::COLUMN_CODE] == 'products') {
            $result = \XLite\Core\Converter::buildURL(
                'product_list',
                '',
                ['action' => 'search', 'substring' => $entity->getName(), 'by_conditions[]' => 'byTag']
            );
        } else {
            $result = parent::buildEntityURL($entity, $column);
        }

        return $result;
    }

    /**
     * Get create entity URL
     *
     * @return string
     */
    protected function getCreateURL()
    {
        return \XLite\Core\Converter::buildURL('tag');
    }

    /**
     * Get create button label
     *
     * @return string
     */
    protected function getCreateButtonLabel()
    {
        return 'New tag';
    }

    /**
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function prevalidateEntity(\XLite\Model\AEntity $entity)
    {
        $result = parent::prevalidateEntity($entity);

        if ($result) {
            $tagRepo = $this->getRepository();

            $cnd = new \XLite\Core\CommonCell();
            $cnd->{$tagRepo::SEARCH_NAME} = $entity->getName();
            $cnd->{$tagRepo::SEARCH_EXCLUDING_ID} = $entity->getId();

            $duplicateTags = $tagRepo->search($cnd);

            if ($duplicateTags) {
                $hasDuplicates = false;

                foreach ($duplicateTags as $duplicateTag) {
                    if ($duplicateTag->getName() ===  $entity->getName()) {
                        $hasDuplicates = true;
                        break;
                    }
                }

                if ($hasDuplicates) {
                    $line = [
                        'id'       => $entity->getId(),
                        'name'     => $entity->getName(),
                        'position' => $entity->getPosition(),
                    ];

                    $this->addLineWithError(
                        $line,
                        'name',
                        static::t('This tag already exists')
                    );

                    $result = false;
                }
            }
        }

        return $result;
    }

    /**
     * @param array  $fields Fields list
     * @param string $key    Field key
     *
     * @return bool
     */
    protected function validateNewEntity($fields, $key)
    {
        $line = $this->getNewDataLine()[$key];

        if ($this->getRepository()->findOneByName($line['name'])) {
            $this->addLineWithError(
                $line,
                'name',
                static::t('This tag already exists')
            );
            return false;
        }

        return parent::validateNewEntity($fields, $key);
    }

    // {{{ Column processing

    /**
     * Get column value for 'products' column
     *
     * @param \XC\ProductTags\Model\Tag $entity tag
     *
     * @return string
     */
    protected function getProductsColumnValue(\XC\ProductTags\Model\Tag $entity)
    {
        return $entity->getProducts()->count();
    }

    // }}}

    // {{{ Behaviors

    /**
     * Inline creation mechanism position
     *
     * @return integer
     */
    protected function isInlineCreation()
    {
        return static::CREATE_INLINE_TOP;
    }

    /**
     * Mark list as removable
     *
     * @return boolean
     */
    protected function isRemoved()
    {
        return true;
    }

    /**
     * Mark list as selectable
     *
     * @return boolean
     */
    protected function isSelectable()
    {
        return true;
    }

    /**
     * Mark list as sortable
     *
     * @return integer
     */
    protected function getSortableType()
    {
        return static::SORT_TYPE_MOVE;
    }

    // }}}

    /**
     * Description for blank items list
     *
     * @return string
     */
    protected function getBlankItemsListDescription()
    {
        return static::t('itemslist.admin.tag.blank');
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' tags';
    }

    /**
     * Get panel class
     *
     * @return string
     */
    protected function getPanelClass()
    {
        return 'XC\ProductTags\View\StickyPanel\ItemsList\Tag';
    }

    /**
     * Return true if lines with errors should be shown
     *
     * @return boolean
     */
    protected function showLinesWithErrors()
    {
        return true;
    }

    /**
     * Return true if duplicates should be renamed
     *
     * @return boolean
     */
    protected function shouldRenameDuplicates()
    {
        return true;
    }

    /**
     * @return array
     */
    protected function findDuplicateNames()
    {
        $repo = $this->getRepository();

        return $repo
            ? $repo->findDuplicateNames()
            : [];
    }

    /**
     * @param string $name
     *
     * @return array
     */
    protected function findDuplicates($name)
    {
        $repo = $this->getRepository();

        return $repo
            ? $repo->findByName($name)
            : [];
    }
}
