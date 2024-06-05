<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Banner\Module\XC\ThemeTweaker\Model\Repo;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Database;

/**
 * @Extender\Mixin
 * @Extender\Depend ("XC\ThemeTweaker")
 */
class ViewList extends \XLite\Model\Repo\ViewList
{
    /**
     * Change banner type on view list change
     *
     * @param string $preset    Layout preset key
     * @param array  $changeset Array of change records
     *
     * @return void
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateOverrides($preset, array $changeset)
    {
        if ($preset && $changeset) {
            foreach ($changeset as &$change) {
                /** @var \XLite\Model\ViewList $entity */
                $entity = $this->find($change['id']);

                if ($entity) {
                    [$newList] = explode(',', $change['list']);

                    $oldList = $entity->getListOverride() ?: $entity->getList();
                    if (
                        preg_match('/BannerSection/', $entity->getChild())
                        && $oldList !== $newList
                        && !empty($newList)
                    ) {
                        $location = $this->detectNewBannerLocation($newList);
                        $bannerLocationPreset = Database::getRepo('\XLite\Model\ViewList')
                            ->findOneBy([
                                'child'    => 'QSL\Banner\View\Customer\BannerSection' . $location,
                                'preset'   => null,
                                'entityId' => null,
                            ]);
                        if ($bannerLocationPreset) {
                            $change['id'] = $bannerLocationPreset->getListId();
                            $change['entityId'] = $entity->getEntityId() ?? 1;
                            if ($entity->getEntityId()) {
                                $entity->delete();
                            }
                        }
                        $this->updateBannerType($newList, $entity->getEntityId());
                    }
                }
            }
        }

        parent::updateOverrides($preset, $changeset);
    }

    protected function updateBannerType($newList, $bannerId)
    {
        if (
            empty($bannerId)
            || !($banner = Database::getRepo('QSL\Banner\Model\Banner')->find($bannerId))
        ) {
            return;
        }

        $maps = [
            'sidebar.first'      => 'MainColumn',
            'sidebar.second'     => 'SecondaryColumn',
            'layout.top.wide'    => 'WideTop',
            'layout.bottom.wide' => 'WideBottom',
        ];

        $newBannerLocation = $maps[$newList] ?? 'StandardTop';
        if (
            $newBannerLocation === 'StandardTop'
            && in_array($banner->getLocation(), ['StandardTop', 'StandardBottom'])
        ) {
            $newBannerLocation = $banner->getLocation();
        }
        if ($banner->getLocation() != $newBannerLocation) {
            $banner->setLocation($newBannerLocation);
            Database::getEM()->persist($banner);
        }
    }

    protected function detectNewBannerLocation(string $list): string
    {
        $mapsLocation = [
            'center'             => 'StandardTop',
            'center.bottom'      => 'StandardBottom',
            'layout.bottom.wide' => 'WideBottom',
            'sidebar.first'      => 'MainColumn',
            'sidebar.second'     => 'SecondaryColumn',
            'layout.top.wide'    => 'WideTop',
        ];

        return $mapsLocation[$list] ?? '';
    }
}
