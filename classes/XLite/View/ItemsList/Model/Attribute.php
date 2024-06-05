<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model;

/**
 * Attributes items list
 */
class Attribute extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Widget param names
     */
    public const PARAM_GROUP = 'group';

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'name' => [
                static::COLUMN_NAME      => $this->getAttributeGroup()
                    ? $this->getAttributeGroup()->getName()
                    : \XLite\Core\Translation::lbl('No group'),
                static::COLUMN_SUBHEADER => $this->getAttributeGroup()
                    ? static::t(
                        'X attributes in group',
                        [
                            'count' => $this->getAttributeGroup()->getAttributesCount()
                        ]
                    )
                    : null,
                static::COLUMN_CLASS     => \XLite\View\FormField\Inline\Input\Text::class,
                static::COLUMN_PARAMS    => ['required' => true],
                static::COLUMN_NO_WRAP   => true,
                static::COLUMN_ORDERBY   => 100,
                static::COLUMN_LINK      => 'attribute',
            ],
            'type' => [
                static::COLUMN_TEMPLATE => 'attributes/parts/type.twig',
                static::COLUMN_ORDERBY  => 200,
            ],
            'displayAbove' => [
                static::COLUMN_CLASS   => 'XLite\View\FormField\Inline\Input\Checkbox\Switcher\Attribute\DisplayAbove',
                static::COLUMN_ORDERBY => 300,
                static::COLUMN_NAME    => static::t('Display option above the price'),
            ],
            'displayMode' => [
                static::COLUMN_NAME      => static::t('Display as'),
                static::COLUMN_HEAD_HELP => static::t('This option applies only to attributes with multiple values'),
                static::COLUMN_CLASS    => \XLite\View\FormField\Inline\Select\AttributeDisplayMode::class,
                static::COLUMN_ORDERBY  => 400,
            ],
        ];
    }

    /**
     * @param array                                       $column
     * @param \XLite\Model\Attribute|\XLite\Model\AEntity $entity
     *
     * @return boolean
     */
    protected function isClassColumnVisible(array $column, \XLite\Model\AEntity $entity)
    {
        $result = parent::isClassColumnVisible($column, $entity);
        if ($column[self::COLUMN_CODE] === 'displayMode') {
            $result = $entity->getType() === \XLite\Model\Attribute::TYPE_SELECT;
        }

        if ($column[self::COLUMN_CODE] === 'displayAbove') {
            $result = $entity->getType() !== \XLite\Model\Attribute::TYPE_HIDDEN;
        }

        return $result;
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Model\Attribute';
    }

    /**
     * Get create entity URL
     *
     * @return string
     */
    protected function getCreateURL()
    {
        return \XLite\Core\Converter::buildUrl('attribute');
    }

    /**
     * Get create button label
     *
     * @return string
     */
    protected function getCreateButtonLabel()
    {
        return 'New attribute';
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_GROUP => new \XLite\Model\WidgetParam\TypeObject(
                'Group',
                null,
                false,
                '\XLite\Model\AttributeGroup'
            ),
        ];
    }

    /**
     * Return class name for the list pager
     *
     * @return string
     */
    protected function getPagerClass()
    {
        return 'XLite\View\Pager\Admin\Model\Infinity';
    }

    /**
     * Get attribute group
     *
     * @return \XLite\Model\AttributeGroup
     */
    protected function getAttributeGroup()
    {
        return $this->getParam(static::PARAM_GROUP);
    }

    // {{{ Behaviors

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
     * Mark list as sortable
     *
     * @return integer
     */
    protected function getSortableType()
    {
        return static::SORT_TYPE_MOVE;
    }

    /**
     * Check if there are any results to display in list
     *
     * @return boolean
     */
    protected function hasResults()
    {
        return true;
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return $this->getAttributeGroup()
            || 0 < $this->getItemsCount();
    }

    /**
     * Check - pager box is visible or not
     *
     * @return boolean
     */
    protected function isPagerVisible()
    {
        return false;
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
        return 'javascript: void(0);';
    }

    /**
     * @return string
     */
    protected function getEditLink()
    {
        return true;
    }

    /**
     * Get edit link params string
     *
     * @param \XLite\Model\AEntity $entity Entity
     * @param array                $column Column data
     *
     * @return string
     */
    protected function getEditLinkAttributes(\XLite\Model\AEntity $entity, array $column)
    {
        $params = [];
        $params[] = 'data-id=' . $entity->getId();

        if ($entity->getProductClass()) {
            $params[] = 'data-class-id=' . $entity->getProductClass()->getId();
        }

        return parent::getEditLinkAttributes($entity, $column) . implode(' ', $params);
    }

    // }}}

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        $class = parent::getContainerClass() . ' attributes';

        if ($this->getAttributeGroup()) {
            $class .= ' group';
        }

        return $class;
    }

    /**
     * Get panel class
     *
     * @return \XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return null;
    }


    // {{{ Search

    /**
     * Return search parameters.
     *
     * @return array
     */
    public static function getSearchParams()
    {
        return [];
    }

    /**
     * Return params list to use for search
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

        $result->productClass = $this->getProductClass();
        if (\XLite\Core\Request::getInstance()->isGet()) {
            $result->attributeGroup = $this->getAttributeGroup();
            $result->productClass = $this->getProductClass();
        }
        $result->product = null;

        return $result;
    }

    // }}}

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
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function prevalidateEntity(\XLite\Model\AEntity $entity)
    {
        $result = parent::prevalidateEntity($entity);

        if ($result) {
            $attributeRepo = $this->getRepository();

            $cnd = new \XLite\Core\CommonCell();
            $cnd->{$attributeRepo::SEARCH_NAME} = $entity->getName();
            $cnd->{$attributeRepo::SEARCH_EXCLUDING_ID} = $entity->getId();
            $cnd->{$attributeRepo::SEARCH_PRODUCT_CLASS} = $entity->getProductClass();
            $cnd->{$attributeRepo::SEARCH_ATTRIBUTE_GROUP} = $entity->getAttributeGroup();
            $cnd->{$attributeRepo::SEARCH_PRODUCT} = null;

            $duplicateAttributes = $attributeRepo->search($cnd);

            if ($duplicateAttributes) {
                $hasDuplicates = false;

                foreach ($duplicateAttributes as $duplicateAttribute) {
                    if ($duplicateAttribute->getName() ===  $entity->getName()) {
                        $hasDuplicates = true;
                        break;
                    }
                }

                if ($hasDuplicates) {
                    $line = [
                        'id'          => $entity->getId(),
                        'name'        => $entity->getName(),
                        'position'    => $entity->getPosition(),
                        'type'        => $entity->getType(),
                        'displayMode' => $entity->getDisplayMode(),
                        'group'       => $entity->getAttributeGroup()
                            ? $entity->getAttributeGroup()->getId()
                            : 'no_group',
                    ];

                    $this->addLineWithError(
                        $line,
                        'name',
                        static::t('This attribute already exists')
                    );

                    $result = false;
                }
            }
        }

        return $result;
    }

    /**
     * @param array  $line
     * @param string $fieldName
     * @param string $errorMessage
     */
    protected function addLineWithError($line, $fieldName, $errorMessage)
    {
        $group = $line['group'];
        unset($line['group']);

        $type = isset($line['id'])
            ? 'existing'
            : 'new';

        $this->linesWithErrors[$group][$type][] = [
            'fields' => $line,
            'error' => [
                'fieldName' => $fieldName,
                'message' => $errorMessage,
            ]
        ];
    }

    /**
     * Clear new lines with errors in session
     */
    protected function clearSavedLinesWithErrors()
    {
        $savedLinesWithErrors = $this->getSavedLinesWithErrors();
        $group = $this->getAttributeGroup()
            ? $this->getAttributeGroup()->getId()
            : 'no_group';

        unset($savedLinesWithErrors[$group]);

        \XLite\Core\Session::getInstance()->set(
            self::SAVED_LINES_WITH_ERRORS,
            $savedLinesWithErrors
        );
    }

    /**
     * Return specific items list parameters that will be sent to JS code
     *
     * @return array
     */
    protected function getItemsListParams()
    {
        $itemsListParams = parent::getItemsListParams();
        $group = $this->getAttributeGroup()
            ? $this->getAttributeGroup()->getId()
            : 'no_group';

        $itemsListParams['linesWithErrors'] = $this->getSavedLinesWithErrors()[$group] ?? [];

        return  $itemsListParams;
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
     * Process renaming of duplicate items
     */
    protected function processRenameDuplicates()
    {
        $attrGroups = $this->getAttributeGroups();
        $attrGroups[] = null;
        $duplicateItemsWereRenamed = false;

        $currentAttrGroup = $this->getAttributeGroup();
        foreach ($attrGroups as $attrGroup) {
            $this->widgetParams[static::PARAM_GROUP]->setValue($attrGroup);

            $duplicateItemsWereRenamed = $duplicateItemsWereRenamed || $this->renameDuplicates();
        }

        $this->widgetParams[static::PARAM_GROUP]->setValue($currentAttrGroup);

        if ($duplicateItemsWereRenamed) {
            $this->postProcessRenameDuplicates();
        }
    }

    /**
     * @return array
     */
    protected function findDuplicateNames()
    {
        $repo = $this->getRepository();

        return $repo
            ? $repo->findDuplicateNames($this->getProductClass(), $this->getAttributeGroup())
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
            ? $repo->findByNameAndProductClassAndAttrGroup($name, $this->getProductClass(), $this->getAttributeGroup())
            : [];
    }
}
