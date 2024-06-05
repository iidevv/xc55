<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoFeatureWidget\View;

use Qualiteam\SkinActVideoFeature\Helpers\Profile;
use Qualiteam\SkinActVideoFeature\Model\EducationalVideo;
use Qualiteam\SkinActVideoFeatureWidget\Main;
use XCart\Extender\Mapping\ListChild;
use XLite\Core\Auth;
use XLite\Core\CommonCell;
use XLite\Core\Database;

/**
 * @ListChild(list="layout.main.center.bottom", zone="customer", weight="10000")
 */
class EducationalVideosList extends \XLite\View\Dialog
{
    const DEFAULT_SORT_MODE = 'ASC';
    const DEFAULT_ORDER_COLUMN = 'pos';

    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();

        $list[] = 'main';

        return $list;
    }

    public function getJSFiles()
    {
        $list   = parent::getJSFiles();

        $list[] = 'modules/Qualiteam/SkinActVideoFeature/js/jquery.lazytube.js';
        $list[] = 'modules/Qualiteam/SkinActVideoFeatureWidget/script.js';

        return $list;
    }

    protected function isVisible()
    {
        return parent::isVisible()
            && $this->isShowWidgetBlock();
    }

    protected function getDir()
    {
        return Main::getModulePath();
    }

    protected function getHead(): string
    {
        return static::t('SkinActVideoFeatureWidget educational videos');
    }

    protected function getSeeAllLabel(): string
    {
        return static::t('SkinActVideoFeatureWidget see all');
    }

    protected function getSeeAllLink(): string
    {
        return $this->buildURL('educational_videos', '', ['show_all_videos' => 1]);
    }

    /**
     * Return link URL
     *
     * @return string
     */
    protected function getDialogLink() : string
    {
        return $this->getSeeAllLink();
    }

    /**
     * Return link label
     *
     * @return string
     */
    protected function getDialogLinkTitle() : string
    {
        return $this->getSeeAllLabel();
    }

    protected function isShowWidgetBlock(): bool
    {
        return !$this->isEmptyList()
            && $this->isUserLogged()
            && $this->isUserProMembership();
    }

    protected function isEmptyList(): bool
    {
        return $this->pageDataItemsCount() === 0;
    }

    protected function isUserLogged(): bool
    {
        return Auth::getInstance()->isLogged();
    }

    protected function isUserProMembership(): bool
    {
        return Profile::isProMembership();
    }

    protected function pageDataItemsCount(): int
    {
        return count($this->getPageData());
    }

    protected function getPageData()
    {
        $cnd = new CommonCell;

        $cnd = $this->preparePageData($cnd);

        return Database::getRepo(EducationalVideo::class)->search($cnd);
    }

    protected function preparePageData(CommonCell $cnd): CommonCell
    {
        $limit = $this->prepareLimitItems();

        if (!empty($limit)) {
            $cnd->limit = $limit;
        }

        $orderBy = $this->prepareOrderByItems();

        if (!empty($orderBy)) {
            $cnd->orderBy = $orderBy;
        }

        return $cnd;
    }

    /**
     * Prepare limit items
     *
     * @return array
     */
    protected function prepareLimitItems(): array
    {
        return $this->getLimitItems() ?: [];
    }

    /**
     * Collect [min, max] items
     *
     * @return array
     */
    protected function getLimitItems(): array
    {
        return [
            $this->getMinLimit(),
            $this->getMaxLimit()
        ];
    }

    /**
     * Get min limit items
     *
     * @return int
     */
    protected function getMinLimit(): int
    {
        return 0;
    }

    /**
     * Get max limit items
     *
     * @return int
     */
    protected function getMaxLimit(): int
    {
        return (int)\XLite\Core\Config::getInstance()->Qualiteam->SkinActVideoFeatureWidget->educational_videos_limit ?: 5;
    }

    /**
     * Prepare orderBy items
     *
     * @return array
     */
    protected function prepareOrderByItems(): array
    {
        return $this->getOrderByItems() ?: [];
    }

    /**
     * Collect orderBy items [column name, sort mode]
     *
     * @return array
     */
    protected function getOrderByItems(): array
    {
        return [
            $this->getDefaultOrderByColumn(),
            $this->getDefaultColumnSort()
        ];
    }

    /**
     * Get default order column
     *
     * @return string
     */
    protected function getDefaultOrderByColumn(): string
    {
        return $this->prepareDefaultOrderByColumn();
    }

    /**
     * Prepare default orderBy column "alias.column_name"
     *
     * @return string
     */
    protected function prepareDefaultOrderByColumn(): string
    {
        return sprintf(
            '%s.%s',
            $this->getDefaultModelAlias(),
            $this->getOrderColumnName()
        );
    }

    /**
     * Get order column name
     *
     * @return string
     */
    protected function getOrderColumnName(): string
    {
        return static::DEFAULT_ORDER_COLUMN;
    }

    /**
     * Get default model alias
     *
     * @return string
     */
    protected function getDefaultModelAlias(): string
    {
        return Database::getRepo(EducationalVideo::class)->getDefaultAlias();
    }

    /**
     * Get default sort mode "ASC\DESC"
     *
     * @return string
     */
    protected function getDefaultColumnSort(): string
    {
        return static::DEFAULT_SORT_MODE;
    }

    protected function getBlockClasses()
    {
        return parent::getBlockClasses() . ' widget-block-educational-videos';
    }

}