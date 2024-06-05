<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Main page controller
 */
class Main extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        return array_merge(
            parent::defineFreeFormIdActions(),
            [
                'hide_welcome_block',
                'close_module_banner',
            ]
        );
    }

    /**
     * Check ACL permissions
     *
     * @return bool
     */
    public function checkACL()
    {
        return true;
    }

    /**
     * Return 'Taxes' url
     *
     * @return string
     */
    public function getTaxesURL()
    {
        return $this->buildURL('tax_classes');
    }

    protected function doActionUpdateInventoryProducts()
    {
        // Update price and other fields
        \XLite\Core\Database::getRepo('\XLite\Model\Product')
            ->updateInBatchById($this->getPostedData());

        \XLite\Core\TopMessage::addInfo(
            'Inventory has been successfully updated'
        );
    }

    protected function doActionHideWelcomeBlock()
    {
        $blockName = \XLite\Core\Request::getInstance()->block;

        $sessionClosedBlocks = \XLite\Core\Session::getInstance()->closedBlocks ?: [];
        $sessionClosedBlocks[$blockName] = true;
        \XLite\Core\Session::getInstance()->closedBlocks = $sessionClosedBlocks;

        if (\XLite\Core\Request::getInstance()->forever) {
            $profileId = \XLite\Core\Auth::getInstance()->getProfile()->getProfileId();
            $foreverClosedBlocks = \XLite\Core\TmpVars::getInstance()->closedBlocks ?: [];
            $profileRecord = $foreverClosedBlocks[$profileId] ?? [];
            $profileRecord[$blockName] = true;
            $foreverClosedBlocks[$profileId] = $profileRecord;
            \XLite\Core\TmpVars::getInstance()->closedBlocks = $foreverClosedBlocks;
        }

        $this->silent = true;
        $this->setSuppressOutput(true);
    }

    protected function doActionCloseModuleBanner()
    {
        $moduleName = \XLite\Core\Request::getInstance()->module;
        $closedModuleBanners = \XLite\Core\TmpVars::getInstance()->closedModuleBanners ?: [];
        $closedModuleBanners[$moduleName] = true;

        \XLite\Core\TmpVars::getInstance()->closedModuleBanners = $closedModuleBanners;

        print ('OK');

        $this->setSuppressOutput(true);
    }
}
