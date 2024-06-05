<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace QSL\Banner\Core;

use XCart\Event\Service\ViewListMutationEvent;

final class EventListener
{
    public function onApplyViewListMutationsAfter(ViewListMutationEvent $event): void
    {
        $childs = [
            'QSL\Banner\View\Customer\BannerSectionMainColumn',
            'QSL\Banner\View\Customer\BannerSectionSecondaryColumn',
            'QSL\Banner\View\Customer\BannerSectionStandardBottom',
            'QSL\Banner\View\Customer\BannerSectionStandardTop',
            'QSL\Banner\View\Customer\BannerSectionWideBottom',
            'QSL\Banner\View\Customer\BannerSectionWideTop',
        ];

        \XLite\Core\Database::getRepo('\XLite\Model\ViewList')->updateOverrideModeByChilds($childs);

        $this->deleteObsoleteViewLists($childs);

        $this->generateOneBannerPerViewList($event->getVersionKey());
    }

    protected function generateOneBannerPerViewList($currentVersionKey = null)
    {
        $allBanners = \XLite\Core\Database::getRepo('\QSL\Banner\Model\Banner')->getAllBanners() ?: [];
        foreach ($allBanners as $banner) {
            $location            = $banner->getLocation();
            $bannerSectionBlocks = \XLite\Core\Database::getRepo('\XLite\Model\ViewList')->findByChildTpl('QSL\Banner\View\Customer\BannerSection' . $location, '') ?: [];

            foreach ($bannerSectionBlocks as $bannerBlock) {
                if (
                    $bannerBlock->child
                    && !$bannerBlock->getVersion()
                    && !$bannerBlock->getPreset()
                    && !$bannerBlock->getEntityId()
                ) {
                    // Hide the parent view list
                    \XLite\Core\Database::getRepo('\XLite\Model\ViewList')->update(
                        $bannerBlock,
                        ['override_mode' => \XLite\Model\ViewList::OVERRIDE_DISABLE_PRESET]
                    );

                    $existingOneBannerPerViewList = \XLite\Core\Database::getRepo('\XLite\Model\ViewList')->findByEntityId($banner->getId()) ?: [];
                    $existingOneBannerPerViewList = !empty($existingOneBannerPerViewList) ? array_shift($existingOneBannerPerViewList) : null;
                    if (
                        $currentVersionKey
                        && $existingOneBannerPerViewList
                    ) {
                        $existingOneBannerPerViewList->setVersion($currentVersionKey);
                    }

                    if (!$existingOneBannerPerViewList) {
                        $newWeight = $bannerSectionBlocks[count($bannerSectionBlocks) - 1]->getWeight() + 5;

                        // Generate a new view list per each banner based on the parent view list
                        $newViewList = $bannerBlock->cloneEntity();
                        $newViewList->setOverrideMode(\XLite\Model\ViewList::OVERRIDE_OFF);
                        $newViewList->setEntityId($banner->getId());
                        $newViewList->setVersion($currentVersionKey);
                        $newViewList->setWeight($newWeight);

                        \XLite\Core\Database::getEM()->persist($newViewList);
                    }
                }
            }
            \XLite\Core\Database::getEM()->flush();
        }
    }

    protected function deleteObsoleteViewLists($locations)
    {
        $allBanners = \XLite\Core\Database::getRepo('\QSL\Banner\Model\Banner')->getAllBanners() ?: [];
        $bannerIds  = $toDelete = [];
        foreach ($allBanners as $banner) {
            $bannerIds[] = $banner->getId();
        }

        foreach ($locations as $location) {
            $bannerSectionBlocks = \XLite\Core\Database::getRepo('\XLite\Model\ViewList')->findByChildTpl($location, '') ?: [];
            foreach ($bannerSectionBlocks as $bannerBlock) {
                if (
                    $bannerBlock->child
                    && $bannerBlock->getEntityId()
                    && !in_array($bannerBlock->getEntityId(), $bannerIds)
                ) {
                    $toDelete[] = $bannerBlock;
                    $needFlush  = true;
                }
            }
        }

        if (!empty($needFlush)) {
            \XLite\Core\Database::getRepo('\XLite\Model\ViewList')->deleteInBatch($toDelete);
        }
    }
}
