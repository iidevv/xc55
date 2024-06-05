<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCreateOrder\View\ItemsList\Model;

/**
 * Profiles items list
 */
class ProfileSelect extends \XLite\View\ItemsList\Model\Profile
{
    protected function getWidgetClass()
    {
        return parent::getWidgetClass() . ' order-profile-select-items-list';
    }

    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), ['user_selection']);
    }

    /**
     * Return wrapper form options
     *
     * @return array
     */
    protected function getFormOptions()
    {
        $options = parent::getFormOptions();

        $options['class'] = '\Qualiteam\SkinActCreateOrder\View\Form\UserSelection\Form';
        $options['target'] = null;
        $options['action'] = null;
        $options['params'] = null;

        return $options;
    }

    /**
     * Get wrapper form target
     *
     * @return string
     */
    protected function getFormTarget()
    {
        return 'user_selection';
    }

    /**
     * Checks if this itemslist is exportable through 'Export all' button
     *
     * @return boolean
     */
    protected function isExportable()
    {
        return false;
    }

    /**
     * Mark list as selectable
     *
     * @return boolean
     */
    protected function isSelectable()
    {
        return false;
    }

    /**
     * Mark list as removable
     *
     * @return boolean
     */
    protected function isRemoved()
    {
        return false;
    }

    /**
     * Creation button position
     *
     * @return integer
     */
    protected function isCreation()
    {
        return static::CREATE_INLINE_NONE;
    }

    protected function isAllowEntityRemove(\XLite\Model\AEntity $entity)
    {
        // Admin user cannot remove own account
        return false;
    }

    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        $result->{\XLite\Model\Repo\Profile::SEARCH_USER_TYPE} = ['C', 'N'];
        $result->{\XLite\Model\Repo\Profile::SEARCH_ORDER_ID} = false;

        return $result;
    }

    /**
     * Define columns structure
     *
     * @return array
     */

    protected function getEntity()
    {
        return $this->entity;
    }

    protected function defineColumns()
    {
        return [
            'selected' => [
                static::COLUMN_ORDERBY => 100,
                static::COLUMN_TEMPLATE => 'modules/Qualiteam/SkinActCreateOrder/user_selection/parts/select.twig',
            ],
            'name' => [
                static::COLUMN_NAME => \XLite\Core\Translation::lbl('Name'),
                static::COLUMN_NO_WRAP => true,
                static::COLUMN_ORDERBY => 200,
                static::COLUMN_TEMPLATE => 'modules/Qualiteam/SkinActCreateOrder/user_selection/parts/name.twig',
            ],
            'added' => array(
                static::COLUMN_NAME => \XLite\Core\Translation::lbl('Created'),
                static::COLUMN_TEMPLATE => $this->getDir() . '/' . $this->getPageBodyDir() . '/profile/cell.added.twig',
                static::COLUMN_ORDERBY => 300,
            ),
            'last_login' => array(
                static::COLUMN_NAME => \XLite\Core\Translation::lbl('Last login'),
                static::COLUMN_TEMPLATE => $this->getDir() . '/' . $this->getPageBodyDir() . '/profile/cell.last_login.twig',
                static::COLUMN_ORDERBY => 400,
            ),
        ];
    }

    /**
     * Get main column
     *
     * @return array
     */
    protected function getMainColumn()
    {
        return null;
    }

    /**
     * Get panel class
     *
     * @return string|\XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return '\Qualiteam\SkinActCreateOrder\View\StickyPanel\Profile\Admin\Profile';
    }

    /**
     * Get search panel widget class
     *
     * @return string
     */
    protected function getSearchPanelClass()
    {
        return '\Qualiteam\SkinActCreateOrder\View\SearchPanel\Profile\Admin\Main';
    }

    /**
     * Return class name for the list pager
     *
     * @return string
     */
    protected function getPagerClass()
    {
        return '\Qualiteam\SkinActCreateOrder\View\Pager\Admin\Model\Table';
    }

    protected function getCommonParams()
    {
        $commonParams = parent::getCommonParams();

        if ($this->getOrder()) {
            $commonParams['order_number'] = $this->getOrder()->getOrderNumber();
        }

        $this->commonParams = $commonParams;

        return $this->commonParams;
    }

    /**
     * Get a list of JavaScript files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        //$list[] = 'modules/Qualiteam/SkinActCreateOrder/js/table.js.js';

        return $list;
    }
}
