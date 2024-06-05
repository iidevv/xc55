<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\EventDriver;

/**
 * DB-based event driver
 */
class Db extends \XLite\Core\EventDriver\AEventDriver
{
    /**
     * Get driver code
     *
     * @return string
     */
    public static function getCode()
    {
        return 'db';
    }

    /**
     * Fire event
     *
     * @param string $name      Event name
     * @param array  $arguments Arguments OPTIONAL
     *
     * @return boolean
     */
    public function fire($name, array $arguments = [])
    {
        $entity = new \XLite\Model\EventTask();
        $entity->setName($name);
        $entity->setArguments($arguments);

        \XLite\Core\Database::getEM()->persist($entity);
        \XLite\Core\Database::getEM()->flush();
    }
}
