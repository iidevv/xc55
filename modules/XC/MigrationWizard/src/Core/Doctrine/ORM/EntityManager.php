<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Core\Doctrine\ORM;

use XCart\Extender\Mapping\Extender;

/**
 * EntityManager
 * @Extender\Mixin
 */
class EntityManager extends \XLite\Core\Doctrine\ORM\EntityManager
{
    /**
     * Transactional with restarts @see transactional()
     *
     * @param callable $func
     * @param callable|null $rollbackCallback used
     * @param int $triesCount 1 equal to transactional()
     *
     * @return mixed
     * @throws \Exception
     */
    public function transactionalWithRestarts(callable $func, $rollbackCallback = null, $triesCount = 3)
    {
        try {
            $this->getConnection()->beginTransaction();

            $return = $func($this);

            //$this->flush();
            $this->getConnection()->commit();

            return $return ?: true;
        } catch (\Exception $e) {
            $this->getConnection()->rollback();
            if (is_callable($rollbackCallback)) {
                $rollbackCallback($this);
            }

            if ($triesCount > 1) {
                return $this->transactionalWithRestarts($func, $rollbackCallback, $triesCount - 1);
            }

            $this->close();
            throw $e;
        }
    }
}
