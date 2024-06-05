<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductStickers\View\ItemsList\Model;

class ProductSticker extends \XLite\View\ItemsList\Model\Table
{
    /**
     * @return array
     */
    protected function defineColumns()
    {
        return [
          'name' => [
              static::COLUMN_CLASS   => 'XLite\View\FormField\Inline\Input\Text',
              static::COLUMN_NAME    => \XLite\Core\Translation::lbl('Sticker text'),
              static::COLUMN_PARAMS  => ['required' => true],
              static::COLUMN_MAIN    => true,
              static::COLUMN_ORDERBY => 100,
          ],
          'text_color' => [
              static::COLUMN_CLASS   => 'XLite\View\FormField\Inline\Input\ColorPicker',
              static::COLUMN_NAME    => \XLite\Core\Translation::lbl('Stickers text color'),
              static::COLUMN_PARAMS  => ['required' => true],
              static::COLUMN_MAIN    => true,
              static::COLUMN_ORDERBY => 200,
          ],
          'bg_color' => [
              static::COLUMN_CLASS   => 'XLite\View\FormField\Inline\Input\ColorPicker',
              static::COLUMN_NAME    => \XLite\Core\Translation::lbl('Stickers background color'),
              static::COLUMN_PARAMS  => ['required' => true],
              static::COLUMN_MAIN    => true,
              static::COLUMN_ORDERBY => 300,
          ]
        ];
    }

    /**
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'QSL\ProductStickers\Model\ProductSticker';
    }

    /**
     * @param \XLite\Core\CommonCell $cnd
     * @param bool                   $countOnly
     *
     * @return array|int
     */
    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        if (\XLite\Core\Config::getInstance()->QSL->ProductStickers->move_labels === false) {
            $cnd->isLabel = false;
        }

        return parent::getData($cnd, $countOnly);
    }

    /**
     * @param array                $column
     * @param \XLite\Model\AEntity $entity
     *
     * @return bool
     */
    protected function isTemplateColumnVisible(array $column, \XLite\Model\AEntity $entity)
    {
        return $entity->isLabel() && $column['class'] !== 'XLite\View\FormField\Inline\Input\ColorPicker'
            ? false
            : parent::isTemplateColumnVisible($column, $entity);
    }

    /**
     * @param array                $column
     * @param \XLite\Model\AEntity $entity
     *
     * @return array
     */
    protected function preprocessFieldParams(array $column, \XLite\Model\AEntity $entity)
    {
        $params = parent::preprocessFieldParams($column, $entity);

        if ($entity->isLabel() && $column['class'] !== 'XLite\View\FormField\Inline\Input\ColorPicker') {
            $params['attributes'] = ['disabled' => true];
        }

        return $params;
    }

    /**
     * @param int                       $index
     * @param \XLite\Model\AEntity|null $entity
     *
     * @return array
     */
    protected function getLineAttributes($index, \XLite\Model\AEntity $entity = null)
    {
        $attributes = parent::getLineAttributes($index, $entity);

        if ($entity->isLabel()) {
            $attributes['class'][] = 'dump-entity';
        }

        return $attributes;
    }

    /**
     * @return int
     */
    protected function getSortableType()
    {
        return static::SORT_TYPE_MOVE;
    }

    /**
     * @return bool
     */
    protected function isSwitchable()
    {
        return true;
    }

    /**
     * @return bool
     */
    protected function isRemoved()
    {
        return true;
    }

    /**
     * @return int
     */
    protected function isInlineCreation()
    {
        return static::CREATE_INLINE_TOP;
    }

    /**
     * @return string
     */
    protected function getCreateButtonLabel()
    {
        return 'New sticker';
    }

    /**
     * @return string
     */
    protected function getCreateURL()
    {
        return \XLite\Core\Converter::buildUrl('product_stickers');
    }

    /**
     * @return int
     */
    protected function update()
    {
        \XLite\Model\Product::removeProductStickerCache();
        return parent::update();
    }

    /**
     * @param \XLite\Model\AEntity $entity
     *
     * @return bool
     */
    protected function prevalidateNewEntity(\XLite\Model\AEntity $entity)
    {
        $validated = parent::prevalidateNewEntity($entity);

        if ($validated) {
            $exists = \XLite\Core\Database::getRepo('QSL\ProductStickers\Model\ProductSticker')
                ->findOneByName($entity->getName());
            if ($exists) {
                \XLite\Core\TopMessage::addError(
                    'Sticker with name {{name}} already exists',
                    [
                        'name' => $entity->getName()
                    ]
                );
                $validated = false;
            }
        }

        return $validated;
    }

    protected function getPanelClass()
    {
        return 'QSL\ProductStickers\View\StickyPanel\ItemsList\ProductSticker';
    }
}
