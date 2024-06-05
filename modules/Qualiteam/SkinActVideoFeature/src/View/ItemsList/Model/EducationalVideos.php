<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoFeature\View\ItemsList\Model;

use Qualiteam\SkinActVideoFeature\Model\EducationalVideo as EducationalVideoModel;
use Qualiteam\SkinActVideoFeature\Model\Repo\EducationalVideo as EducationalVideoRepo;
use Qualiteam\SkinActVideoFeature\View\StickyPanel\EducationalVideo\Admin\StickyPanel as EducationalVideosStickyPanel;

class EducationalVideos extends \XLite\View\ItemsList\Model\Table
{
    public const PARAM_CATEGORY_ID       = 'categoryId';

    public const SORT_BY_MODE_DESC   = 'translations.description';
    public const SORT_BY_MODE_POS   = 'e.pos';
    public const SORT_BY_MODE_CATEGORY   = 'ct.name';

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/Qualiteam/SkinActVideoFeature/items_list/style.less';

        return $list;
    }

    /**
     * Define and set widget attributes; initialize widget
     *
     * @param array $params Widget params OPTIONAL
     */
    public function __construct(array $params = [])
    {
        $this->sortByModes += [
            static::SORT_BY_MODE_DESC   => 'Description',
            static::SORT_BY_MODE_POS    => 'Position',
            static::SORT_BY_MODE_CATEGORY    => 'Category',
        ];

        parent::__construct($params);
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return EducationalVideoModel::class;
    }

    /**
     * Get list name suffixes
     *
     * @return array
     */
    protected function getListNameSuffixes()
    {
        return ['educational_videos'];
    }

    /**
     * Get remove message
     *
     * @param integer $count Count
     *
     * @return string
     */
    protected function getRemoveMessage($count)
    {
        return static::t('SkinActVideoFeature x videos has been removed', ['count' => $count]);
    }

    /**
     * Get create message
     *
     * @param integer $count Count
     *
     * @return string
     */
    protected function getCreateMessage($count)
    {
        return static::t('SkinActVideoFeature x videos has been created', ['count' => $count]);
    }

    /**
     * Description for blank items list
     *
     * @return string
     */
    protected function getBlankItemsListDescription()
    {
        return static::t('SkinActVideoFeature videos blank');
    }

    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    protected function checkACL()
    {
        return parent::checkACL()
            && \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage catalog');
    }

    /**
     * Get search condition for update all found actions
     *
     * @return \XLite\Core\CommonCell
     */
    public function getActionSearchCondition()
    {
        $cnd = $this->getSearchCondition();
        unset($cnd->orderBy);

        return $cnd;
    }

    /**
     * Return wrapper form options
     *
     * @return array
     */
    protected function getFormOptions()
    {
        return array_merge(parent::getFormOptions(), [
            \XLite\View\Form\AForm::PARAM_CONFIRM_REMOVE => true
        ]);
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' educational_videos';
    }

    protected function getSearchPanelClass()
    {
        return '';
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
        return 'educational_videos';
    }

    protected function defineColumns()
    {
        return [
            'position' => [
                static::COLUMN_NAME    => \XLite\Core\Translation::lbl('Position'),
                static::COLUMN_CLASS     => \XLite\View\FormField\Inline\Input\Text::class,
                static::COLUMN_NO_WRAP => true,
                static::COLUMN_SORT    => static::SORT_BY_MODE_POS,
                static::COLUMN_ORDERBY => 100,
            ],
            'description'     => [
                static::COLUMN_NAME    => \XLite\Core\Translation::lbl('Description'),
                static::COLUMN_MAIN    => true,
                static::COLUMN_NO_WRAP => false,
                static::COLUMN_SORT    => static::SORT_BY_MODE_DESC,
                static::COLUMN_ORDERBY => 200,
                static::COLUMN_LINK    => 'educational_video',
            ],
            'category' => [
                static::COLUMN_NAME    => \XLite\Core\Translation::lbl('Category'),
                static::COLUMN_NO_WRAP => true,
                static::COLUMN_SORT    => static::SORT_BY_MODE_CATEGORY,
                static::COLUMN_ORDERBY => 300,
            ],
        ];
    }

    /**
     * Get create entity URL
     *
     * @return string
     */
    protected function getCreateURL()
    {
        return $this->buildURL('educational_video');
    }

    /**
     * Get create button label
     *
     * @return string
     */
    protected function getCreateButtonLabel()
    {
        return static::t('SkinActVideoFeature new video');
    }

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
     * Get panel class
     *
     * @return string|\XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return EducationalVideosStickyPanel::class;
    }

    protected function isExportable()
    {
        return false;
    }

    protected function getSortByModeDefault()
    {
        return static::SORT_BY_MODE_DESC;
    }

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
     * Mark list as switchable (enable / disable)
     *
     * @return bool
     */
    protected function isSwitchable()
    {
        return true;
    }

    /**
     * Mark list as selectable
     *
     * @return bool
     */
    protected function isSelectable()
    {
        return true;
    }

    /**
     * Preprocess category
     *
     * @param mixed                 $value  Value
     * @param array                 $column Column data
     * @param EducationalVideoModel $entity Video
     *
     * @return string
     */
    protected function preprocessCategory($value, array $column, EducationalVideoModel $entity)
    {
        return $value
            ? func_htmlspecialchars($value->getName())
            : '';
    }

    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        $categoryId = \XLite\Core\Request::getInstance()->categoryId;

        if ($categoryId) {
            $cnd->{EducationalVideoRepo::P_CATEGORY_ID} = $categoryId;
        }

        return parent::getData($cnd, $countOnly);
    }
}