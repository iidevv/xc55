<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\View\ItemsList\Model;

use Qualiteam\SkinActAftership\Traits\AftershipTrait;
use Qualiteam\SkinActAftership\Model\ShipstationCodeMapping;
use Qualiteam\SkinActAftership\View\FormField\Inline\Select\Select2\Courier;
use XLite\Core\Auth;
use XLite\View\Form\AForm;
use XLite\View\FormField\Inline\Input\Text;
use XLite\View\StickyPanel\ItemsListForm;

class CodeMapping extends \XLite\View\ItemsList\Model\Table
{
    use AftershipTrait;

    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();

        $list[] = static::getCodeMappingConfigName();

        return $list;
    }

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = $this->getModulePath() . '/items_list/style.less';

        return $list;
    }

    protected function wrapWithFormByDefault()
    {
        return true;
    }

    protected function getFormTarget()
    {
        return static::getCodeMappingConfigName();
    }

    protected function getListNameSuffixes()
    {
        return [static::getCodeMappingConfigName()];
    }

    protected function getRemoveMessage($count)
    {
        return static::t('SkinActAftership x items has been removed', ['count' => $count]);
    }

    protected function getCreateMessage($count)
    {
        return static::t('SkinActAftership x items has been created', ['count' => $count]);
    }

    protected function checkACL()
    {
        return parent::checkACL()
            && Auth::getInstance()->isPermissionAllowed('manage catalog');
    }

    protected function getFormOptions()
    {
        return array_merge(parent::getFormOptions(), [
            AForm::PARAM_CONFIRM_REMOVE => true,
        ]);
    }

    protected function isInlineCreation()
    {
        return static::CREATE_INLINE_TOP;
    }

    protected function getSearchPanelClass()
    {
        return '';
    }

    protected function isCreation()
    {
        return static::CREATE_INLINE_TOP;
    }

    protected function isExportable()
    {
        return false;
    }

    protected function isRemoved()
    {
        return true;
    }

    protected function isSwitchable()
    {
        return false;
    }

    protected function isSelectable()
    {
        return false;
    }

    protected function defineRepositoryName(): string
    {
        return ShipstationCodeMapping::class;
    }

    protected function getBlankItemsListDescription()
    {
        return static::t('SkinActAftership table is empty');
    }

    protected function getPanelClass()
    {
        return ItemsListForm::class;
    }

    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' shipstation-code-mapping';
    }

    protected function getCreateButtonLabel()
    {
        return static::t('SkinActAftership add condition');
    }

    /**
     * @inheritDoc
     */
    protected function defineColumns()
    {
        return [
            'shipstation_slug' => [
                static::COLUMN_NAME    => static::t('SkinActAftership shipstation slug'),
                static::COLUMN_CLASS   => Text::class,
                static::COLUMN_ORDERBY => 100,
            ],
            'aftership_slug'  => [
                static::COLUMN_NAME    => static::t('SkinActAftership aftership slug'),
                static::COLUMN_CLASS   => Courier::class,
                static::COLUMN_ORDERBY => 200,
            ],
        ];
    }
}