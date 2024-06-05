<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model;

class ProductClass extends \XLite\View\ItemsList\Model\Table
{
    public static function getAllowedTargets()
    {
        $list   = parent::getAllowedTargets();
        $list[] = 'product_classes';

        return $list;
    }

    /**
     * Should itemsList be wrapped with form
     *
     * @return bool
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
        return 'product_classes';
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
                static::COLUMN_CLASS        => 'XLite\View\FormField\Inline\Input\Text\ProductClass',
                static::COLUMN_PARAMS       => ['required' => true],
                static::COLUMN_MAIN         => true,
                static::COLUMN_ORDERBY      => 100,
            ],
            'attributes' => [
                static::COLUMN_TEMPLATE      => 'product_classes/parts/edit_attributes.twig',
                static::COLUMN_HEAD_TEMPLATE => 'product_classes/parts/edit_attributes.twig',
                static::COLUMN_ORDERBY       => 200,
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
        return \XLite\Model\ProductClass::class;
    }

    /**
     * Get create button label
     *
     * @return string
     */
    protected function getCreateButtonLabel()
    {
        return 'New product class';
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
     * @return bool
     */
    protected function prevalidateEntity(\XLite\Model\AEntity $entity)
    {
        $result = parent::prevalidateEntity($entity);

        if ($result) {
            $productClassRepo = $this->getRepository();

            $cnd = new \XLite\Core\CommonCell();
            $cnd->{$productClassRepo::CND_NAME} = $entity->getName();
            $cnd->{$productClassRepo::CND_EXCLUDING_ID} = $entity->getId();

            $duplicateProductClasses = $productClassRepo->search($cnd);

            if ($duplicateProductClasses) {
                $hasDuplicates = false;

                foreach ($duplicateProductClasses as $duplicateProductClass) {
                    if ($duplicateProductClass->getName() ===  $entity->getName()) {
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
                        static::t('This product class already exists')
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
                static::t('This product class already exists')
            );

            return false;
        }

        return parent::validateNewEntity($fields, $key);
    }


    // {{{ Behaviors

    /**
     * Mark list as removable
     *
     * @return bool
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
        return parent::getContainerClass() . ' product_classes';
    }

    /**
     * Get panel class
     *
     * @return \XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return 'XLite\View\StickyPanel\ItemsList\ProductClass';
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

        return $result;
    }

    // }}}

    /**
     * Return attributes count.
     *
     * @param mixed $entity Model
     *
     * @return integer
     */
    protected function getAttributesCount($entity)
    {
        if ($entity && $entity->isPersistent()) {
            $result = $entity->getAttributesCount();
        } else {
            $cnd = new \XLite\Core\CommonCell();
            $cnd->productClass = null;
            $cnd->product = null;
            $result = \XLite\Core\Database::getRepo('XLite\Model\Attribute')->search($cnd, true);
        }

        return $result;
    }

    /**
     * Return edit url.
     *
     * @param mixed $entity Model
     *
     * @return string
     */
    protected function getEditURL($entity)
    {
        return $entity && $entity->getId()
            ? $this->buildURL('attributes', '', ['product_class_id' => $entity->getId()])
            : $this->buildURL('attributes');
    }

    /**
     * Get label for edit link
     *
     * @param mixed $entity Model
     *
     * @return string
     */
    protected function getEditLinkLabel($entity)
    {
        return static::t('Edit attributes');
    }

    /*
     * Get empty list template
     *
     * @return string
     */
    protected function getEmptyListTemplate()
    {
        return 'product_classes/empty.twig';
    }

    /**
     * Return true if lines with errors should be shown
     *
     * @return bool
     */
    protected function showLinesWithErrors()
    {
        return true;
    }

    /**
     * Return true if duplicates should be renamed
     *
     * @return bool
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
