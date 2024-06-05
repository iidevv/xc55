<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\View\ThemeTweaker;

use Includes\Utils\Module\Manager;
use XCart\Extender\Mapping\ListChild;
use XLite\Core\Layout;

/**
 * Main panel of layout editing mode
 *
 * @ListChild (list="themetweaker-panel--content", weight="100")
 */
class LayoutEditor extends \XLite\View\AView
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/XC/ThemeTweaker/themetweaker/layout_editor';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/panel.twig';
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = $this->getDir() . '/panel_style.less';
        $list[] = $this->getDir() . '/panel_parts/banners/layout_banners.less';

        return $list;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = $this->getDir() . '/panel_parts/layout_options.js';
        $list[] = $this->getDir() . '/panel_parts/layout_groups.js';
        $list[] = $this->getDir() . '/panel_parts/blocks_list.js';
        $list[] = $this->getDir() . '/panel_parts/banners/layout_banners.js';
        $list[] = $this->getDir() . '/layout_editor_panel.js';

        return $list;
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->isInLayoutMode();
    }

    /**
     * @return bool
     */
    protected function isNotOptimalTarget()
    {
        return !in_array($this->getTarget(), ['main', 'category'], true);
    }

    /**
     * Get finishOperateAs action url
     *
     * @return string
     */
    protected function getFinishOperateAsUrl()
    {
        return $this->buildURL('login', 'logoff');
    }

    /**
     * Returns current used layout preset key
     * @return string
     */
    protected function getCurrentLayoutPreset()
    {
        return Layout::getInstance()->getCurrentLayoutPreset();
    }

    /**
     * @return string
     */
    protected function getGroupsLayoutPreset()
    {
        $preset = $this->getCurrentLayoutPreset();

        $first = Layout::getInstance()->isSidebarFirstVisible();
        $second = Layout::getInstance()->isSidebarSecondVisible();

        if (!$first) {
            switch ($preset) {
                case Layout::LAYOUT_THREE_COLUMNS:
                    $preset = Layout::LAYOUT_TWO_COLUMNS_RIGHT;
                    break;
                case Layout::LAYOUT_TWO_COLUMNS_LEFT:
                    $preset = Layout::LAYOUT_ONE_COLUMN;
                    break;
            }
        }

        if (!$second) {
            switch ($preset) {
                case Layout::LAYOUT_THREE_COLUMNS:
                    $preset = Layout::LAYOUT_TWO_COLUMNS_LEFT;
                    break;
                case Layout::LAYOUT_TWO_COLUMNS_RIGHT:
                    $preset = Layout::LAYOUT_ONE_COLUMN;
                    break;
            }
        }

        return $preset;
    }

    /**
     * Check if logo configuration is available
     *
     * @return bool
     */
    protected function isSimpleCMSEnabled()
    {
        return Manager::getRegistry()->isModuleEnabled('CDev\SimpleCMS');
    }

    /**
     * Check if we should show the "Logo & Favicon" section
     *
     * @return bool
     */
    protected function shouldShowHeaderSection()
    {
        return !\XC\ThemeTweaker\Core\ThemeTweaker::isInCheckout();
    }

    /**
     * @return boolean
     */
    protected function isResetAvailable()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\ViewList')->hasOverriddenRecords($this->getCurrentLayoutPreset());
    }

    /**
     * @param string $key Field name
     *
     * @return \XLite\Model\Image\Common\Logo
     */
    protected function getImageObject($key)
    {
        /** @var \XLite\Model\Repo\Image\Common\Logo $repo */
        $repo = \XLite\Core\Database::getRepo('XLite\Model\Image\Common\Logo');

        switch ($key) {
            case 'logo':
                return $repo->getLogo();
            case 'favicon':
                return $repo->getFavicon();
            case 'appleIcon':
                return $repo->getAppleIcon();
            default:
                return null;
        }
    }

    /**
     * @param $name
     *
     * @return string
     */
    protected function getLayoutEditorSVGIcon($name)
    {
        return $this->getSVGImage($this->getDir() . '/icons/' . $name);
    }

    /**
     * @return string
     */
    protected function getImageMaxWidth()
    {
        return '70';
    }

    /**
     * @return string
     */
    protected function getImageMaxHeight()
    {
        return '70';
    }

    /**
     * @return bool
     */
    protected function isMainPage()
    {
        return \XLite::getController()->getTarget() === 'main';
    }

    protected function getBannerBoxesSB()
    {
        return Manager::getRegistry()->isModuleEnabled('QSL\Banner')
            ? (new \XC\ThemeTweaker\Module\QSL\Banner\View\BannerSectionAll())->getBannerBoxesAll()
            : [];
    }
}
