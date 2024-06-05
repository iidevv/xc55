<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\View;

use XCart\Extender\Mapping\ListChild;

/**
 * Widget that renders the "Rules" section on the "Loyalty Program Details" page.
 *
 * @ListChild (list="loyalty-program-join", zone="customer", weight="100")
 * @ListChild (list="sidebar.second", zone="customer", weight="99")
 */
class LoyaltyProgramJoin extends \XLite\View\SideBarBox
{
    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result   = parent::getAllowedTargets();
        $result[] = 'loyalty_program_details';

        return $result;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/QSL/LoyaltyProgram/loyalty_program_page/join.css';

        return $list;
    }

    /**
     * Get widge title
     *
     * @return string
     */
    protected function getHead()
    {
        return 'Join Loyalty Program Now!';
    }

    /**
     * Register the CSS classes for this block
     *
     * @return string
     */
    protected function getBlockClasses()
    {
        return parent::getBlockClasses() . ' block-join-loyalty-program';
    }

    /**
     * Get widget templates directory
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/QSL/LoyaltyProgram/loyalty_program_page';
    }

    /**
     * Return file name for body template
     *
     * @return string
     */
    protected function getBodyTemplate()
    {
        return 'join.twig';
    }

    /**
     * Check if widget is visible.
     *
     * @return boolean
     */
    protected function isVisible()
    {
        $profile           = \XLite\Core\Auth::getInstance()->getProfile();
        $enabledForProfile = (!$profile || !$profile->isLoyaltyProgramEnabled());

        // Display the widget in the following view lists:
        // single column - loyalty-program-join
        // left sidebar - loyalty-program-join
        // right sidebar - sidebar.second
        // two sidebars - sidebar.second
        $list                = $this->viewListName;
        $layout              = \XLite\Core\Layout::getInstance();
        $rightSidebarVisible = $layout->isSidebarSecondVisible();
        $visibleInList       =  (($list == 'sidebar.second') && $rightSidebarVisible)
            || (($list == 'loyalty-program-join') && !$rightSidebarVisible);

        return $enabledForProfile && $visibleInList;
    }
}
