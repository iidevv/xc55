<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoFeature\View\ItemsList\Model;

use Qualiteam\SkinActVideoFeature\Model\Repo\EducationalVideo as EducationalVideoRepo;
use Qualiteam\SkinActVideoFeature\Model\Repo\VideoCategory as VideoCategoryRepo;
use Qualiteam\SkinActVideoFeature\Model\VideoCategory as VideoCategoryModel;

class VideoCategories extends \XLite\View\ItemsList\Model\Table
{

    public const IS_DISPLAY_REMOVAL_NOTICE = 'is_display_removal_notice';

    /**
     * Create counter
     *
     * @var integer
     */
    protected $createCount = 0;

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/Qualiteam/SkinActVideoFeature/items_list/model/style.less';

        return $list;
    }

    /**
     * Get a list of JS files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/Qualiteam/SkinActVideoFeature/items_list/model/controller.js';

        if ($this->isDisplayRemovalNoticePopup()) {
            $list[] = 'modules/Qualiteam/SkinActVideoFeature/items_list/model/removal_notice_popup.js';
        }

        return $list;
    }

    /**
     * Check if removal notice popup should be displayed
     *
     * @return bool
     */
    public function isDisplayRemovalNoticePopup()
    {
        return (bool) \XLite\Core\Session::getInstance()->{self::IS_DISPLAY_REMOVAL_NOTICE};
    }

    /**
     * Description for blank items list
     *
     * @return string
     */
    protected function getEmptyListDescription()
    {
        return $this->isRootCategory()
            ? static::t('SkinActVideoFeature categories blank')
            : static::t('SkinActVideoFeature subcategories blank');
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
        return 'video_category';
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
                'id' => \XLite\Core\Request::getInstance()->id,
            ]
        );
    }

    /**
     * Return name of the session cell identifier
     *
     * @return string
     */
    public function getSessionCell()
    {
        return parent::getSessionCell() . $this->getCategory()->getCategoryId();
    }

    /**
     * Get widget parameters
     *
     * @return array
     */
    protected function getWidgetParameters()
    {
        $list       = parent::getWidgetParameters();
        $list['id'] = $this->getCategory()->getCategoryId();

        return $list;
    }

    /**
     * Define so called "request" parameters
     *
     * @return void
     */
    protected function defineRequestParams()
    {
        parent::defineRequestParams();

        $this->requestParams[] = 'id';
    }

    /**
     * Get URL common parameters
     *
     * @return array
     */
    protected function getCommonParams()
    {
        parent::getCommonParams();

        $this->commonParams['id'] = $this->getCategory()->getCategoryId();

        return $this->commonParams;
    }

    /**
     * Get category
     *
     * @return VideoCategoryModel
     */
    protected function getCategory()
    {
        return \XLite\Core\Request::getInstance()->id
            ? \XLite\Core\Database::getRepo(VideoCategoryModel::class)->find((int)\XLite\Core\Request::getInstance()->id)
            : \XLite\Core\Database::getRepo(VideoCategoryModel::class)->getRootCategory();
    }

    /**
     * Check - current category is root cCategory or not
     *
     * @return boolean
     */
    protected function isRootCategory()
    {
        return !\XLite\Core\Request::getInstance()->id
            || \XLite\Core\Request::getInstance()->id == \XLite\Core\Database::getRepo(VideoCategoryModel::class)->getRootCategoryId();
    }

    /**
     * Get formatted path of current category
     *
     * @return string
     */
    protected function getFormattedPath()
    {
        $list = [];

        foreach ($this->getCategory()->getPath() as $category) {
            $list[] = '<a href="' . static::buildURL('video_categories', '', ['id' => $category->getCategoryId()]) . '">'
                . func_htmlspecialchars($category->getName())
                . '</a>';
        }

        return implode(' :: ', $list);
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = [
            'image'         => [
                static::COLUMN_NAME         => '',
                static::COLUMN_CLASS        => 'XLite\View\FormField\Inline\FileUploader\Image',
                static::COLUMN_CREATE_CLASS => 'XLite\View\FormField\Inline\EmptyField',
                static::COLUMN_PARAMS       => ['required' => false],
                static::COLUMN_ORDERBY      => 100,
            ],
            'name'          => [
                static::COLUMN_NAME         => \XLite\Core\Translation::lbl('SkinActVideoFeature category'),
                static::COLUMN_CREATE_CLASS => 'XLite\View\FormField\Inline\Input\Text',
                static::COLUMN_PARAMS       => ['required' => true],
                static::COLUMN_ORDERBY      => 200,
                static::COLUMN_NO_WRAP      => true,
                static::COLUMN_MAIN         => true,
                static::COLUMN_LINK         => 'video_category',
            ],
            'subcategories' => [
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('SkinActVideoFeature subcat'),
                static::COLUMN_TEMPLATE => false,
                static::COLUMN_ORDERBY  => 300,
            ],
            'info'          => [
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('SkinActVideoFeature videos'),
                static::COLUMN_TEMPLATE => false,
                static::COLUMN_ORDERBY  => 400,
            ],
        ];

        if ($this->getCategory()->getDepth() === 0) {
            unset($columns['subcategories']);
        }

        return $columns;
    }

    /**
     * Create entity
     *
     * @return \XLite\Model\AEntity
     */
    protected function createEntity()
    {
        $entity = parent::createEntity();

        $parent = null;
        if (\XLite\Core\Request::getInstance()->id) {
            $parent = \XLite\Core\Database::getRepo(VideoCategoryModel::class)->find((int)\XLite\Core\Request::getInstance()->id);
        }

        if (!$parent) {
            $parent = \XLite\Core\Database::getRepo(VideoCategoryModel::class)->getRootCategory();
        }

        $entity->setParent($parent);

        return $entity;
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
        // Resort
        $pos = 10;
        $entity->setPos($pos);
        foreach ($entity->getParent()->getChildren() as $child) {
            $pos += 10;
            $child->setPos($pos);
        }
        $this->createCount++;
        parent::insertNewEntity($entity);

        \XLite\Core\Database::getRepo(VideoCategoryModel::class)->correctCategoriesStructure();
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return VideoCategoryModel::class;
    }

    /**
     * Get create entity URL
     *
     * @return string
     */
    protected function getCreateURL()
    {
        return \XLite\Core\Converter::buildUrl(
            'video_category',
            null,
            [
                'parent' => $this->getCategory()->getCategoryId(),
            ]
        );
    }

    /**
     * Get create button label
     *
     * @return string
     */
    protected function getCreateButtonLabel()
    {
        return static::t('SkinActVideoFeature new video category button');
    }

    // {{{ Behaviors

    /**
     * Creation button position
     *
     * @return integer
     */
    protected function isCreation()
    {
        return static::CREATE_INLINE_TOP;
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
     * Mark list as removable
     *
     * @return boolean
     */
    protected function isRemoved()
    {
        return true;
    }

    /**
     * Mark list as switchyabvle (enable / disable)
     *
     * @return boolean
     */
    protected function isSwitchable()
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
     * @inheritdoc
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' video-categories';
    }

    /**
     * @inheritdoc
     */
    protected function getPanelClass()
    {
        return 'Qualiteam\SkinActVideoFeature\View\StickyPanel\ItemsList\VideoCategory';
    }

    /**
     * Check - table header is visible or not
     *
     * @return boolean
     */
    protected function isHeaderVisible()
    {
        return true;
    }

    /**
     * isFooterVisible
     *
     * @return boolean
     */
    protected function isFooterVisible()
    {
        return true;
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
        return \XLite\Core\Converter::buildURL(
            $column[static::COLUMN_LINK],
            '',
            ['id' => $entity->getUniqueIdentifier()]
        );
    }

    /**
     * Preprocess name
     *
     * @param string             $value  Value
     * @param array              $column Column data
     * @param VideoCategoryModel $entity Video
     *
     * @return string
     */
    protected function preprocessName($value, array $column, VideoCategoryModel $entity)
    {
        return func_htmlspecialchars($value);
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

        $result->{VideoCategoryRepo::SEARCH_PARENT} = \XLite\Core\Request::getInstance()->id
            ? (int)\XLite\Core\Request::getInstance()->id
            : \XLite\Core\Database::getRepo(VideoCategoryModel::class)->getRootCategoryId();

        return $result;
    }

    /**
     * Returns condition to use in videos count table
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getVideosCountCondition()
    {
        $cnd = new \XLite\Core\CommonCell();

        $cnd->{EducationalVideoRepo::P_SEARCH_IN_SUBCATS} = true;

        return $cnd;
    }

    // }}}

    /**
     * getSortByModeDefault
     *
     * @return string
     */
    protected function getSortByModeDefault()
    {
        return 'c.pos';
    }
}