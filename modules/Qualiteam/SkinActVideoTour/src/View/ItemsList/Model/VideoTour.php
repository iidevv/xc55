<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoTour\View\ItemsList\Model;

use Qualiteam\SkinActVideoTour\Model\Repo\VideoTours;
use Qualiteam\SkinActVideoTour\Model\VideoTours as VideoToursModel;
use Qualiteam\SkinActVideoTour\Trait\VideoTourTrait;
use XCart\Extender\Mapping\ListChild;
use XLite\Core\CommonCell;
use XLite\Core\Converter;
use XLite\Core\Request;
use XLite\Core\Translation;
use XLite\View\FormField\Inline\Input\Checkbox\Switcher\OnOff;

/**
 * Class video tours
 */
class VideoTour extends \XLite\View\ItemsList\Model\Table
{
    use VideoTourTrait;

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
            ['video_tour', 'product']
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
     * Get a list of CSS files
     *
     * @return array
     */
    public function getCSSFiles(): array
    {
        $list   = parent::getCSSFiles();
        $list[] = $this->getModulePath() . '/button/less/edit_video_tour.less';

        return $list;
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName(): string
    {
        return VideoToursModel::class;
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
        return 'v.position';
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
            ['v.id', 'asc'],
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
        return 'video_tour';
    }

    /**
     * @return bool
     */
    protected function isExportable(): bool
    {
        return false;
    }

    /**
     * Get right actions templates
     *
     * @return array
     */
    protected function getRightActions(): array
    {
        $list = parent::getRightActions();

        array_unshift(
            $list,
            $this->getModulePath() . '/items_list/model/table/video_tour/action.link.twig'
        );

        return $list;
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
            //'video_url'   => [
            //    static::COLUMN_NAME    => Translation::lbl('SkinActVideoTour video url'),
            //    static::COLUMN_TEMPLATE => $this->getModulePath() . '/video_tours/cell/video_url.twig',
            //    static::COLUMN_ORDERBY => 100,
            //],
            'description' => [
                static::COLUMN_NAME     => Translation::lbl('SkinActVideoTour video description'),
                static::COLUMN_TEMPLATE => $this->getModulePath() . '/video_tours/cell/description.twig',
                static::COLUMN_ORDERBY  => 200,
            ],
            'enabled'     => [
                static::COLUMN_NAME    => Translation::lbl('SkinActVideoTour video enabled'),
                static::COLUMN_CLASS   => OnOff::class,
                static::COLUMN_ORDERBY => 300,
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
        return Converter::buildURL('video_tour');
    }

    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition(): CommonCell
    {
        $result                               = parent::getSearchCondition();
        $result->{VideoTours::SEARCH_PRODUCT} = $this->getProductId();

        return $result;
    }

    /**
     * Get AJAX-specific URL parameters
     *
     * @return array
     */
    protected function getAJAXSpecificParams(): array
    {
        $params                           = parent::getAJAXSpecificParams();
        $params[static::PARAM_PRODUCT_ID] = $this->getProductId();

        return $params;
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
        return parent::getContainerClass() . ' video_tours';
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