<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\HorizontalCategoriesMenu\View;

use XCart\Extender\Mapping\ListChild;

/**
 * Categories list
 *
 * @ListChild (list="header.flycategories", zone="customer", weight="100")
 * @ListChild (list="header.mobile_flycategories", zone="customer", weight="100")
 */
class HorizontalCategories extends \XLite\View\SideBarBox
{
    use DataProvider\Categories;

    /**
     * Widget parameter names
     */
    public const PARAM_ROOT_ID      = 'rootId';
    public const PARAM_IS_SUBTREE   = 'is_subtree';
    public const PARAM_PARENT_FLYOUT_COLUMNS  = 'parentFlyoutColumns';

    /**
     * Current category path id list
     *
     * @var array
     */
    protected $pathIds;

    /**
     * Collection of categories DTOs
     * @var array
     */
    protected $categories = null;

    /**
     * categoriesPath runtime cache
     * @var array
     */
    protected static $categoriesPath;

    /**
     * Preprocess DTO
     *
     * @param  array    $categoryDTO
     * @return array
     */
    protected function preprocessDTO($categoryDTO)
    {

        $categoryDTO['link']                = $this->buildURL('category', '', ['category_id' => $categoryDTO['id']]);
        $categoryDTO['hasSubcategories']    = 0 < $categoryDTO['subcategoriesCount'];
        $categoryDTO['children']            = [];

        if (
            $categoryDTO['hasSubcategories']
            && (!isset($categoryDTO['flyoutColumns']) || !$categoryDTO['flyoutColumns'])
        ) {
            $categoryDTO['flyoutColumns'] = \XLite\Core\Config::getInstance()->QSL->HorizontalCategoriesMenu->hfcm_default_columns_count;
        }

        if (!$categoryDTO['name']) {
            $categoryDTO['name'] = $this->getFirstTranslatedName($categoryDTO['id']);
        }

        return $categoryDTO;
    }

    /**
     * Get category image
     *
     * @param integer $iid Image ID
     *
     * @return \XLite\Model\Image\Category\Image
     */
    public function getCategoryImageById($iid = null)
    {
        return $iid ? \XLite\Core\Database::getRepo('XLite\Model\Image\Category\Image')->find($iid) : null;
    }

    /**
     * Get name fallback
     *
     * @param integer $categoryId Category id
     *
     * @return string
     */
    protected function getFirstTranslatedName($categoryId)
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Category')
            ->getFirstTranslatedName($categoryId);
    }

    /**
     * Get cache parameters for preprocessed DTOs
     *
     * @return array
     */
    protected function getProcessedDTOsCacheParameters()
    {
        $cacheParameters = [
            'categoriesDTOs',
            \XLite\Core\Session::getInstance()->getLanguage()
                ? \XLite\Core\Session::getInstance()->getLanguage()->getCode()
                : '',
            \XLite\Core\Database::getRepo('XLite\Model\Category')->getVersion(),
            LC_USE_CLEAN_URLS
        ];

        if ($this->isShowProductNum()) {
            $cacheParameters[] = \XLite\Core\Database::getRepo('XLite\Model\Product')->getVersion();
        }

        $cacheParameters[] = \XLite\Core\Config::getInstance()->QSL->HorizontalCategoriesMenu->hfcm_default_columns_count;
        $cacheParameters[] = \XLite\Core\Config::getInstance()->QSL->HorizontalCategoriesMenu->hfcm_category_menu_type;

        $auth = \XLite\Core\Auth::getInstance();
        if (
            $auth->isLogged()
            && $auth->getProfile()->getMembership()
        ) {
            $cacheParameters[] = $auth->getProfile()->getMembership()->getMembershipId();
        }

        return $cacheParameters;
    }

    /**
     * Check if category included into active trail or not
     *
     * @param integer $categoryId Category id
     *
     * @return boolean
     */
    protected function isActiveTrail($categoryId)
    {
        if ($this->pathIds === null) {
            $this->pathIds = [];

            if (static::$categoriesPath === null) {
                static::$categoriesPath = \XLite\Core\Database::getRepo('\XLite\Model\Category')
                    ->getCategoryPath($this->getCategoryId());
            }

            if (is_array(static::$categoriesPath)) {
                foreach (static::$categoriesPath as $cat) {
                    $this->pathIds[] = $cat->getCategoryId();
                }
            }
        }

        return in_array($categoryId, $this->pathIds);
    }

    /**
     * Display item CSS class name as HTML attribute
     *
     * @param integer               $index    Item number
     * @param integer               $count    Items count
     * @param array                 $category Current category
     *
     * @return string
     */
    public function displayItemClass($index, $count, $category)
    {
        $className = $this->assembleItemClassName($index, $count, $category);

        return $className ? ' class="' . $className . '"' : '';
    }

    /**
     * Display item link class name as HTML attribute
     *
     * @param integer               $i        Item number
     * @param integer               $count    Items count
     * @param array                 $category Current category
     *
     * @return string
     */
    public function displayLinkClass($i, $count, $category)
    {
        $className = $this->assembleLinkClassName($i, $count, $category);

        return $className ? ' class="' . $className . '"' : '';
    }

    /**
     * Display item children container class as HTML attribute
     *
     * @param integer           $i      Item number
     * @param integer           $count  Items count
     * @param \XLite\View\AView $widget Current category
     *
     * @return string
     */
    public function displayListItemClass($i, $count, \XLite\View\AView $widget)
    {
        $className = $this->assembleListItemClassName($i, $count, $widget);

        return $className ? ' class="' . $className . '"' : '';
    }

    /**
     * Get widget templates directory
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/QSL/HorizontalCategoriesMenu/';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getTemplate()
    {
        $template = 'body.twig';

        if ($this->isMulticolView()) {
            $template = $this->isTwoSublevelsMulticolView()
                ? 'multicol_second_body.twig'
                : 'multicol_body.twig';
        }

        return $this->getDir() . $template;
    }

    public function rootId(): ?int
    {
        return $this->getDefaultCategoryId();
    }

    /**
     * ID of the default root category
     */
    protected function getDefaultCategoryId(): ?int
    {
        return \XLite\Core\Config::getInstance()->QSL->HorizontalCategoriesMenu->hfcm_category_menu_type === 'catalog' ? null : $this->getRootCategoryId();
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $rootId = $this->getDefaultCategoryId();

        $this->widgetParams += [
            self::PARAM_ROOT_ID => new \XLite\Model\WidgetParam\ObjectId\Category(
                'Parent category ID (leave "' . $rootId . '" for root categories list)',
                $rootId,
                true,
                true
            ),
            self::PARAM_IS_SUBTREE => new \XLite\Model\WidgetParam\TypeBool(
                'Is subtree',
                false,
                false
            ),
            self::PARAM_PARENT_FLYOUT_COLUMNS => new \XLite\Model\WidgetParam\TypeInt(
                'Parent flyoutcolumns value',
                0,
                false
            ),

        ];
    }

    /**
     * Checks whether it is a subtree
     *
     * @return boolean
     */
    protected function isSubtree()
    {
        return $this->getParam(self::PARAM_IS_SUBTREE) !== false;
    }

    /**
     *
     *
     * @return int
     */
    protected function getParentFlyoutColumn()
    {
        return $this->getParam(self::PARAM_PARENT_FLYOUT_COLUMNS);
    }

    /**
     * Assemble item CSS class name
     *
     * @param integer               $index    Item number
     * @param integer               $count    Items count
     * @param array                 $category Current category
     *
     * @return string
     */
    protected function assembleItemClassName($index, $count, $category)
    {
        $active = $this->isActiveTrail($category['id']);

        $classes = ['leaf'];

        if (
            $category['hasSubcategories']
            && $this->isNotDeep($category['depth'])
            && count($this->getCategories($category['id'])) > 0
        ) {
            $classes[] = 'has-sub';
        }

        if ($index === 0 && !\XLite\Core\Config::getInstance()->QSL->HorizontalCategoriesMenu->hfcm_show_home) {
            $classes[] = 'first';
        }

        $listParam = [
            'rootId'     => $this->getParam('rootId'),
            'is_subtree' => $this->getParam('is_subtree'),
        ];

        if (
            ($count - 1) === $index
            && $this->isViewListVisible('topCategories.children', $listParam)
        ) {
            $classes[] = 'last';
        }

        if ($active) {
            $classes[] = 'active-trail active';
        }

        return implode(' ', $classes);
    }

    /**
     * Assemble list item link class name
     *
     * @param integer               $i        Item number
     * @param integer               $count    Items count
     * @param array                 $category Current category
     *
     * @return string
     */
    protected function assembleLinkClassName($i, $count, $category)
    {
        $classes = [];

        $classes[] = \XLite\Core\Request::getInstance()->category_id == $category['id']
            ? 'active'
            : '';

        if ($this->isWordWrapDisabled()) {
            $classes[] = 'no-wrap';
        }

        return implode(' ', $classes);
    }

    /**
     * Assemble item children container class name
     *
     * @param integer           $i      Item number
     * @param integer           $count  Items count
     * @param \XLite\View\AView $widget Current category FIXME! this variable is not used
     *
     * @return string
     */
    protected function assembleListItemClassName($i, $count, \XLite\View\AView $widget)
    {
        $classes = ['leaf'];

        if (($count - 1) === $i) {
            $classes[] = 'last';
        }

        return implode(' ', $classes);
    }

    // {{{ Cache

    /**
     * Cache availability
     *
     * @return boolean
     */
    protected function isCacheAvailable()
    {
        return true;
    }

    /**
     * Get cache parameters
     *
     * @return array
     */
    protected function getCacheParameters()
    {
        $list = parent::getCacheParameters();

        $list[] = $this->getCategoryId();

        $auth = \XLite\Core\Auth::getInstance();
        $list[] = ($auth->isLogged() && $auth->getProfile()->getMembership())
            ? $auth->getProfile()->getMembership()->getMembershipId()
            : '-';

        return $list;
    }

    // }}}

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return true;
    }

    /**
     * Check if display subcategories
     *
     * @return boolean
     */
    protected function isRootOnly()
    {
        return $this->viewListName === 'header.mobile_flycategories';
    }

    /**
     * Check if display number of products
     *
     * @return boolean
     */
    protected function isShowProductNum()
    {
        return \XLite\Core\Config::getInstance()->QSL->HorizontalCategoriesMenu->hfcm_show_product_num;
    }

    /**
     * Check if word wrap disabled
     *
     * @return boolean
     */
    protected function isWordWrapDisabled()
    {
        return !\XLite\Core\Config::getInstance()->QSL->HorizontalCategoriesMenu->hfcm_word_wrap;
    }

    /**
     * is multicolumn layout selected
     *
     * @return boolean
     */
    public function isMulticolView()
    {
        return \XLite\Core\Config::getInstance()->QSL->HorizontalCategoriesMenu->hfcm_use_multicolumn;
    }

    /**
     * is multicolumn layout selected
     *
     * @return boolean
     */
    public function isTwoSublevelsMulticolView()
    {
        return \XLite\Core\Config::getInstance()->QSL->HorizontalCategoriesMenu->hfcm_use_second_sublevel;
    }

    /**
     * Get column width setting (for multicolumn layout only)
     *
     * @return boolean
     */
    public function getColumnWidth()
    {
        return \XLite\Core\Config::getInstance()->QSL->HorizontalCategoriesMenu->hfcm_column_multi_width;
    }

    /**
     * Check insert gap
     *
     * @param integer    $idxx       service counter (place # within category)
     * @param integer    $colsCount  Needed column number
     * @param integer    $countt     Count of subcategories
     *
     * @return boolean
     */
    protected function isMulticolGap($idxx, $colsCount, $countt)
    {
        $gap = false;

        if ($colsCount > 0) {
            $i = $idxx + 1;
            $perCol = ceil($countt / $colsCount);
            $rest = $countt - $i;
            $restCols = $colsCount - (floor($i / $perCol)) - 1;

            $gap = ($i % $perCol === 0 || ($i >= $rest && $rest <= $restCols)) && $i !== $countt;
        }

        return $gap;
    }

    /**
     *  multicolumn subcategories block width (px)
     *
     * @param integer $cols
     * @param integer $size
     *
     * @return integer
     */
    protected function multicolFlyoutBlockWidth($cols, $size)
    {
        return min($size, $cols) * $this->getColumnWidth();
    }

    /**
     * Check if category depth doesnt exceed nesting level
     * @param  integer $depth Category depth
     *
     * @return boolean
     */
    protected function isNotDeep($depth)
    {
        return $depth < \XLite\Core\Config::getInstance()->QSL->HorizontalCategoriesMenu->hfcm_depth;
    }
}
