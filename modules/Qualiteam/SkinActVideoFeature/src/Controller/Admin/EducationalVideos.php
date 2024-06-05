<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoFeature\Controller\Admin;

use Qualiteam\SkinActVideoFeature\Model\EducationalVideo as EducationalVideoModel;
use Qualiteam\SkinActVideoFeature\View\ItemsList\Model\EducationalVideos as EducationalVideosItemsListModel;
use XLite\Controller\Features\SearchByFilterTrait;

class EducationalVideos extends \XLite\Controller\Admin\AAdmin
{
    use SearchByFilterTrait;

    /**
     * @return string
     */
    public function getTitle()
    {
        return static::t('SkinActVideoFeature educational videos');
    }

    /**
     * Get itemsList class
     *
     * @return string
     */
    public function getItemsListClass()
    {
        return parent::getItemsListClass() ?: EducationalVideosItemsListModel::class;
    }

    protected function doNoAction()
    {
        parent::doNoAction();

        if (\XLite\Core\Request::getInstance()->fast_search) {
            // Clear stored filter within stored search conditions
            \XLite\Core\Session::getInstance()->{$this->getSessionCellName()} = [];

            // Refresh search parameters from the request
            $this->fillSearchValuesStorage();

            // Get ItemsList widget
            $widget = $this->getItemsList();

            // Search for single video entity
            $entity = $widget->searchForSingleEntity();

            if ($entity && $entity instanceof EducationalVideoModel) {
                // Prepare redirect to video page
                $url = $this->buildURL('educational_video', '', ['id' => $entity->getVideoId()]);
                $this->setReturnURL($url);
            }
        }
    }

    protected function doActionEnable()
    {
        $select = \XLite\Core\Request::getInstance()->select;

        if ($select && is_array($select)) {
            \XLite\Core\Database::getRepo(EducationalVideoModel::class)->updateInBatchById(
                array_fill_keys(
                    array_keys($select),
                    ['enabled' => true]
                )
            );
            \XLite\Core\TopMessage::addInfo(
                'SkinActVideoFeature video information has been successfully updated'
            );
        } elseif ($ids = $this->getActionVideosIds()) {
            $qb    = \XLite\Core\Database::getRepo(EducationalVideoModel::class)->createQueryBuilder();
            $alias = $qb->getMainAlias();
            $qb->update(EducationalVideoModel::class, $alias)
                ->set("{$alias}.enabled", $qb->expr()->literal(true))
                ->andWhere($qb->expr()->in("{$alias}.id", $ids))
                ->execute();
            \XLite\Core\TopMessage::addInfo('SkinActVideoFeature video information has been successfully updated');
        } else {
            \XLite\Core\TopMessage::addWarning('SkinActVideoFeature please select the video first');
        }
    }

    protected function doActionDisable()
    {
        $select = \XLite\Core\Request::getInstance()->select;

        if ($select && is_array($select)) {
            \XLite\Core\Database::getRepo(EducationalVideoModel::class)->updateInBatchById(
                array_fill_keys(
                    array_keys($select),
                    ['enabled' => false]
                )
            );
            \XLite\Core\TopMessage::addInfo(
                'SkinActVideoFeature video information has been successfully updated'
            );
        } elseif ($ids = $this->getActionVideosIds()) {
            $qb    = \XLite\Core\Database::getRepo(EducationalVideoModel::class)->createQueryBuilder();
            $alias = $qb->getMainAlias();
            $qb->update(EducationalVideoModel::class, $alias)
                ->set("{$alias}.enabled", $qb->expr()->literal(false))
                ->andWhere($qb->expr()->in("{$alias}.id", $ids))
                ->execute();
            \XLite\Core\TopMessage::addInfo('SkinActVideoFeature video information has been successfully updated');
        } else {
            \XLite\Core\TopMessage::addWarning('SkinActVideoFeature please select the video first');
        }
    }

    protected function doActionDelete()
    {
        $select = \XLite\Core\Request::getInstance()->select;

        if ($select && is_array($select)) {
            \XLite\Core\Database::getRepo(EducationalVideoModel::class)->deleteInBatchById($select);
            \XLite\Core\TopMessage::addInfo('SkinActVideoFeature videos information has been successfully deleted');
        } elseif ($ids = $this->getActionVideosIds()) {
            \XLite\Core\Database::getRepo(EducationalVideoModel::class)->deleteInBatchById(array_flip($ids));
            \XLite\Core\TopMessage::addInfo('SkinActVideoFeature videos information has been successfully deleted');
        } else {
            \XLite\Core\TopMessage::addWarning('SkinActVideoFeature please select the video first');
        }
    }

    /**
     * @return array
     */
    protected function getActionVideosIds()
    {
        $cnd = $this->getItemsList()->getActionSearchCondition();
        $ids = \XLite\Core\Database::getRepo(EducationalVideoModel::class)
            ->search($cnd, \XLite\Model\Repo\ARepo::SEARCH_MODE_IDS);
        $ids = is_array($ids) ? array_unique(array_filter($ids)) : [];

        return $ids;
    }

    protected function doActionSearchItemsList()
    {
        // Clear stored search conditions
        \XLite\Core\Session::getInstance()->{$this->getSessionCellName()} = [];

        parent::doActionSearchItemsList();

        $this->setReturnURL($this->getURL(['mode' => 'search', 'searched' => 1]));
    }

    /**
     * Return search parameters for video list.
     * It is based on search params from Video Items list viewer
     *
     * @return array
     */
    protected function getSearchParams()
    {
        return parent::getSearchParams()
            + $this->getSearchParamsCheckboxes();
    }

    /**
     * Return search parameters for video list given as checkboxes: (0, 1) values
     *
     * @return array
     */
    protected function getSearchParamsCheckboxes()
    {
        $videosSearchParams = [];

        $itemsListClass = $this->getItemsListClass();
        $cBoxFields     = [
            $itemsListClass::PARAM_BY_TITLE,
            $itemsListClass::PARAM_BY_DESCR,
        ];

        foreach ($cBoxFields as $requestParam) {
            $videosSearchParams[$requestParam] = isset(\XLite\Core\Request::getInstance()->$requestParam) ? 1 : 0;
        }

        return $videosSearchParams;
    }
}