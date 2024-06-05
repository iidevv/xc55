<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\Model\Repo;

use Doctrine\ORM\QueryBuilder;
use XC\ThemeTweaker\Core\ThemeTweaker;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Cache\ExecuteCachedTrait;
use XLite\Model\QueryBuilder\AQueryBuilder;

/**
 * View list repository
 *
 * @Extender\Mixin
 */
class ViewList extends \XLite\Model\Repo\ViewList
{
    use ExecuteCachedTrait;

    /**
     * Applies the override changeset for a certain layout preset
     *
     * @param string $preset Layout preset key
     * @param array $changeset Array of change records
     *
     * @return void
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateOverrides($preset, array $changeset)
    {
        if ($preset && $changeset) {
            foreach ($changeset as $change) {
                /** @var \XLite\Model\ViewList $entity */
                $entity = $this->find($change['id']);

                if ($entity) {
                    if ($entity->getPreset() !== $preset) {
                        /** @var \XLite\Model\ViewList $presetRecord */
                        $presetRecord = $entity->cloneEntity();
                        $presetRecord->setParent($entity);
                        $presetRecord->setPreset($preset);

                        if (isset($change['entityId'])) {
                            $presetRecord->setEntityId($change['entityId']);
                        }

                        \XLite\Core\Database::getEM()->persist($presetRecord);

                        $entity = $presetRecord;
                    }

                    [$list] = explode(',', $change['list']);

                    $entity->applyOverrides(
                        $change['mode'],
                        $list,
                        $change['weight']
                    );
                }
            }

            $this->cleanCache();
            \XLite\Core\Database::getEM()->flush();
        }
    }

    /**
     * Resets all overridden records for a certain layout preset
     *
     * @param string $preset Layout preset key
     *
     * @return boolean
     */
    public function hasOverriddenRecords($preset)
    {
        return $this->defineCountOverriddenRecords($preset)->getSingleScalarResult() > 0;
    }

    /**
     * @param string $preset
     * @param string $zone
     *
     * @return QueryBuilder|AQueryBuilder
     */
    protected function defineCountOverriddenRecords($preset, $zone = null)
    {
        if ($zone === null) {
            $zone = \XLite::CUSTOMER_INTERFACE;
        }

        $modes = [
            \XLite\Model\ViewList::OVERRIDE_MOVE,
            \XLite\Model\ViewList::OVERRIDE_HIDE,
            \XLite\Model\ViewList::OVERRIDE_DISABLE_PRESET
        ];

        return $this->createPureQueryBuilder('v')
            ->select('COUNT(v)')
            ->andWhere('v.version IS NULL')
            ->andWhere('v.preset LIKE :preset')
            ->andWhere('v.zone LIKE :zone')
            ->andWhere('v.override_mode IN (:modes)')
            ->setParameter('modes', $modes)
            ->setParameter('zone', $zone)
            ->setParameter('preset', $preset);
    }

    /**
     * Resets all overridden records for a certain layout preset
     *
     * @param string $preset Layout preset key
     *
     * @return void
     */
    public function resetOverrides($preset)
    {
        $this->defineOverrideResetQuery($preset)->execute();
        $this->cleanCache();
    }

    /**
     * @param string $preset
     * @param string $zone
     *
     * @return QueryBuilder|AQueryBuilder
     */
    protected function defineOverrideResetQuery($preset, $zone = null)
    {
        if ($zone === null) {
            $zone = \XLite::CUSTOMER_INTERFACE;
        }

        return $this->createPureQueryBuilder()
            ->update($this->_entityName, 'v')
            ->set('v.override_mode', \XLite\Model\ViewList::OVERRIDE_OFF)
            ->andWhere('v.version IS NULL')
            ->andWhere('v.preset LIKE :preset')
            ->andWhere('v.zone LIKE :zone')
            ->setParameter('zone', $zone)
            ->setParameter('preset', $preset);
    }

    /**
     * @return array
     */
    protected function getDisplayableModes()
    {
        $modes = parent::getDisplayableModes();

        if (ThemeTweaker::getInstance()->isInLayoutMode()) {
            $modes[] = \XLite\Model\ViewList::OVERRIDE_HIDE;
        }

        return $modes;
    }

    /**
     * Find class list
     *
     * @param string $list List name
     * @param string $interface
     * @param string $zone
     *
     * @return array
     */
    public function findClassList($list, $interface = \Xlite::INTERFACE_WEB, $zone = \XLite::ZONE_CUSTOMER)
    {
        return ThemeTweaker::getInstance()->isInLayoutMode()
            ? $this->retrieveClassList($list, $interface, $zone)
            : parent::findClassList($list, $interface, $zone);
    }

    protected function getDisabledTpl(): array
    {
        return $this->executeCachedRuntime(static function () {
            $disabledTpl = \XLite\Core\Database::getRepo('XC\ThemeTweaker\Model\Template')->findBy([
                'enabled' => false
            ]);

            return array_map(
                static fn ($template): string => str_replace('web/customer/', '', $template->getTemplate()),
                $disabledTpl
            );
        });
    }
}
