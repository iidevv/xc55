<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActMagicImages\View\ItemsList\Model;

use Qualiteam\SkinActMagicImages\Model\MagicSwatchesSet as MagicSwatchesSetModel;
use Qualiteam\SkinActMagicImages\Model\Repo\MagicSwatchesSet as MagicSwatchesSetRepo;
use Qualiteam\SkinActMagicImages\Traits\MagicImagesTrait;
use XLite\Core\CommonCell;
use XLite\Core\Converter;
use XLite\Core\Request;
use XLite\Core\Translation;

/**
 * Class video tours
 */
class MagicSwatchesSet extends \XLite\View\ItemsList\Model\Table
{
    use MagicImagesTrait;

    /**
     * Widget param names
     */
    public const PARAM_PRODUCT_ID = 'product_id';

    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets(): array
    {
        return array_merge(
            parent::getAllowedTargets(),
            ['magic360', 'product']
        );
    }

    /**
     * Check - search panel is visible or not
     *
     * @return boolean
     */
    public function isSearchVisible(): bool
    {
        return false;
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName(): string
    {
        return MagicSwatchesSetModel::class;
    }

    /**
     * Mark list as sortable
     *
     * @return integer
     */
    protected function getSortableType(): int
    {
        return static::SORT_TYPE_MOVE;
    }

    /**
     * getSortByModeDefault
     *
     * @return string
     */
    protected function getSortByModeDefault(): string
    {
        return 'm.id';
    }

    /**
     * Return 'Order by' array.
     * array(<Field to order>, <Sort direction>)
     *
     * @return array
     */
    protected function getOrderBy(): array
    {
        return [
            parent::getOrderBy(),
            ['m.id', 'asc'],
        ];
    }

    /**
     * Should itemsList be wrapped with form
     *
     * @return boolean
     */
    protected function wrapWithFormByDefault(): bool
    {
        return true;
    }

    /**
     * Get search panel widget class
     *
     * @return string|null
     */
    protected function getSearchPanelClass(): ?string
    {
        return null;
    }

    /**
     * Get wrapper form target
     *
     * @return string
     */
    protected function getFormTarget(): string
    {
        return $this->getTargetController();
    }

    /**
     * @return bool
     */
    protected function isExportable(): bool
    {
        return false;
    }

    /**
     * Get wrapper form params
     *
     * @return array
     */
    protected function getFormParams(): array
    {
        $params = [];

        $productId = Request::getInstance()->product_id;
        if ($productId) {
            $params['product_id'] = $productId;
        }

        return array_merge(
            parent::getFormParams(),
            $params
        );
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns(): array
    {
        return [
            'name'           => [
                static::COLUMN_NAME    => Translation::lbl('SkinActMagicImages name'),
                static::COLUMN_CLASS   => 'XLite\View\FormField\Inline\Input\Text',
                static::COLUMN_MAIN    => true,
                static::COLUMN_NO_WRAP => true,
                static::COLUMN_ORDERBY => 200,
            ],
            'attributeValue' => [
                static::COLUMN_NAME    => Translation::lbl('SkinActMagicImages swatch'),
                static::COLUMN_CLASS   => 'Qualiteam\SkinActMagicImages\View\FormField\Swatches',
                static::COLUMN_ORDERBY => 300,
            ],
            'images360count' => [
                static::COLUMN_NAME     => Translation::lbl('SkinActMagicImages images 360'),
                static::COLUMN_ORDERBY  => 400,
                static::COLUMN_TEMPLATE => $this->getModulePath() . '/items_list/model/table/imagesCount.twig',
            ],
        ];
    }

    /**
     * Get create entity URL
     *
     * @return string
     */
    protected function getCreateURL(): string
    {
        return Converter::buildURL('product', '', ['page' => 'magic360']);
    }

    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition(): CommonCell
    {
        $result                                         = parent::getSearchCondition();
        $result->{MagicSwatchesSetRepo::SEARCH_PRODUCT} = $this->getProductId();

        return $result;
    }

    /**
     * isFooterVisible
     *
     * @return boolean
     */
    protected function isFooterVisible(): bool
    {
        return true;
    }

    /**
     * Mark list as removable
     *
     * @return boolean
     */
    protected function isRemoved(): bool
    {
        return true;
    }

    /**
     * Mark list as selectable
     *
     * @return boolean
     */
    protected function isSelectable(): bool
    {
        return false;
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass(): string
    {
        return parent::getContainerClass() . ' magic_images';
    }

    /**
     * Check - table header is visible or not
     *
     * @return boolean
     */
    protected function isHeaderVisible(): bool
    {
        return true;
    }
}