<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model;

use XCart\Extender\Mapping\ListChild;

/**
 * Attribute groups items list
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class AttributeGroup extends \XLite\View\ItemsList\Model\Table
{
    public const PARAM_PRODUCT_CLASS_ID = 'product_class_id';

    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), ['attribute_groups']);
    }

    /**
     * Return additional panel style
     *
     * @return string
     */
    protected function getAdditionalPanelStyle()
    {
        $result = 'additional-panel';

        if (\XLite\Core\Database::getRepo('XLite\Model\AttributeGroup')->count() == 0) {
            $result .= ' hidden';
        }

        return $result;
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'attribute_groups/style.css';

        return $list;
    }

    /**
     * Should itemsList be wrapped with form
     *
     * @return boolean
     */
    protected function wrapWithFormByDefault()
    {
        return true;
    }

    /**
     * Get wrapper form target
     *
     * @return string
     */
    protected function getFormTarget()
    {
        return 'attribute_groups';
    }

    /**
     * Get product_class_id from request
     *
     * @return int
     */
    protected function getProductClassId(): int
    {
        return (int) \XLite\Core\Request::getInstance()->{static::PARAM_PRODUCT_CLASS_ID};
    }

    /**
     * Get wrapper form params
     *
     * @return array
     */
    protected function getFormParams()
    {
        return array_merge(
            parent::getFormParams(),
            [
                'product_class_id' => $this->getProductClassId(),
            ]
        );
    }

    /**
     * Get bottom actions
     *
     * @return array
     */
    protected function getBottomActions()
    {
        $actions = parent::getBottomActions();

        $actions[] = 'attribute_groups/submit.twig';

        return $actions;
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'name' => [
                static::COLUMN_CLASS    => 'XLite\View\FormField\Inline\Input\Text',
                static::COLUMN_MAIN     => true,
                static::COLUMN_NO_WRAP  => true,
                static::COLUMN_PARAMS   => ['required' => true],
                static::COLUMN_ORDERBY  => 100,
            ],
        ];
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Model\AttributeGroup';
    }

    /**
     * Get create button label
     *
     * @return string
     */
    protected function getCreateButtonLabel()
    {
        return 'New group';
    }

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
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function prevalidateEntity(\XLite\Model\AEntity $entity)
    {
        $result = parent::prevalidateEntity($entity);

        if ($result) {
            $attrGroupRepo = $this->getRepository();

            $cnd = new \XLite\Core\CommonCell();
            $cnd->{$attrGroupRepo::SEARCH_NAME} = $entity->getName();
            $cnd->{$attrGroupRepo::SEARCH_EXCLUDING_ID} = $entity->getId();
            $cnd->{$attrGroupRepo::SEARCH_PRODUCT_CLASS} = $this->getProductClass();

            $duplicateAttrGroups = $attrGroupRepo->search($cnd);

            if ($duplicateAttrGroups) {
                $hasDuplicates = false;

                foreach ($duplicateAttrGroups as $duplicateAttrGroup) {
                    if ($duplicateAttrGroup->getName() ===  $entity->getName()) {
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
                        static::t('This group already exists')
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

        if (
            $this->getRepository()->findOneByNameAndProductClass(
                $line['name'],
                $this->getProductClass()
            )
        ) {
            $this->addLineWithError(
                $line,
                'name',
                static::t('This group already exists')
            );

            return false;
        }

        return parent::validateNewEntity($fields, $key);
    }

    /**
     * Remove entity
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function removeEntity(\XLite\Model\AEntity $entity)
    {
        foreach ($entity->getAttributes() as $attribute) {
            $attribute->setName(
                $attribute->getName() . '_' . $entity->getName()
            );

            $attribute->setAttributeGroup(null);
        }

        return parent::removeEntity($entity);
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

    // }}}

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' attribute_groups';
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

    /**
     * Create entity
     *
     * @return \XLite\Model\AEntity
     */
    protected function createEntity()
    {
        $entity = parent::createEntity();

        $entity->setProductClass($this->getProductClass());

        return $entity;
    }

    /**
     * Check - pager box is visible or not
     *
     * @return boolean
     */
    protected function isPagerVisible()
    {
        return true;
    }

    /**
     * Return class name for the list pager
     *
     * @return string
     */
    protected function getPagerClass()
    {
        return 'XLite\View\Pager\Admin\Model\ManageAttributeGroups';
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
            ? $repo->findDuplicateNames($this->getProductClass())
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
            ? $repo->findByNameAndProductClass($name, $this->getProductClass())
            : [];
    }

    /**
     * @return array
     */
    protected function getAJAXSpecificParams()
    {
        $params = parent::getAJAXSpecificParams();
        $params[static::PARAM_PRODUCT_CLASS_ID] = $this->getProductClassId();

        return $params;
    }
}
