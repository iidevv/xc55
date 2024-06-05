<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Model\Repo;

use Qualiteam\SkinActGraphQLApi\Model;
use Qualiteam\SkinActGraphQLApi\Model\Device as DeviceModel;

/**
 * Device repository
 */
class Device extends \XLite\Model\Repo\ARepo
{
    const P_UNIQUE_ID       = 'uniqueId';
    const P_APPLICATION_ID  = 'applicationId';

    /**
     * @param string $uniqueId Unique device ID
     *
     * @return DeviceModel
     * @throws \Doctrine\ORM\ORMException
     */
    public function findOrCreateDeviceById($uniqueId)
    {
        $device = $this->findOneBy([
            'unique_id' => $uniqueId,
        ]);

        if (!($device instanceof DeviceModel)) {
            $device = new DeviceModel();
            $device->setUniqueId($uniqueId);
            \XLite\Core\Database::getEM()->persist($device);
        }

        return $device;
    }

    /**
     * Get device by application and unique device ID
     *
     * @param string $appId    Application
     * @param string $uniqueId Push ID
     *
     * @return DeviceModel | null
     */
    public function findOneByUniqueId($appId, $uniqueId)
    {
        $cnd = new \XLite\Core\CommonCell();
        $cnd->{static::P_APPLICATION_ID} = $appId;
        $cnd->{static::P_UNIQUE_ID} = $uniqueId;
        $cnd->{static::P_LIMIT} = array(0, 1);

        $result = $this->search($cnd);

        return count($result) > 0 ? $result[0] : null;
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndUniqueId(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $queryBuilder->andWhere('d.unique_id = :unique_id')
            ->setParameter('unique_id', $value);
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndApplicationId(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $queryBuilder->andWhere('d.app_id = :app_id')
            ->setParameter('app_id', $value);
    }
}
