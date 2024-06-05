<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Doctrine\ORM;

use Doctrine\DBAL\TransactionIsolationLevel;

class EntityManager extends \Doctrine\ORM\Decorator\EntityManagerDecorator
{
    /**
     * @var bool
     */
    protected $flushInProgress = false;

    /**
     * @var bool
     */
    protected $afterFlushCallbacksInProgress = false;

    /**
     * @var array
     */
    protected $afterFlushCallbacks = [];

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

            $this->flush();
            $this->getConnection()->commit();

            return $return ?: true;
        } catch (\Exception $e) {
            $this->getConnection()->rollBack();
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

    /**
     * @param $func
     *
     * @return bool|mixed
     * @throws \Exception
     */
    public function transactional($func)
    {
        $this->getConnection()->setTransactionIsolation(TransactionIsolationLevel::READ_COMMITTED);

        return parent::transactional($func);
    }

    /**
     * Add callback which could possibly has flush inside
     *
     * @param callable $func
     */
    public function addAfterFlushCallback(callable $func)
    {
        $this->afterFlushCallbacks[] = $func;
    }

    /**
     * Try to execute afterFlushCallbacks.
     * afterFlushCallbacks must be executed only in main flush
     */
    protected function tryExecuteAfterFlushCallbacks()
    {
        if (!$this->afterFlushCallbacksInProgress) {
            $this->afterFlushCallbacksInProgress = true;
            while (count($this->afterFlushCallbacks) > 0) {
                $callback = array_shift($this->afterFlushCallbacks);
                $callback();
            }
            $this->afterFlushCallbacksInProgress = false;
        }
    }

    /**
     * @param null|object|array $entity
     *
     * @throws \Exception
     */
    public function flush($entity = null): void
    {
        try {
            if (!$this->flushInProgress) {
                $this->flushInProgress = true;
                parent::flush($entity);
                $this->flushInProgress = false;
            } else {
                throw FlushAlreadyStartedException::flushAlreadyStarted();
            }
        } catch (FlushAlreadyStartedException $e) {
            \XLite\Logger::getInstance()->registerException($e);
            throw $e;
        } catch (\Exception $e) {
            if (
                $e instanceof \Doctrine\DBAL\Exception\DriverException
                && ($query = $e->getQuery())
            ) {
                $message = $e->getMessage();
                $sql = $query->getSQL();

                $prepareParams = '';
                if ($params = $query->getParams()) {
                    $prepareParams = [];
                    foreach ($params as $key => $value) {
                        $prepareParams[] = "$key: $value";
                    }

                    $prepareParams = implode(', ', $prepareParams);
                }

                $message .= " ($sql, $prepareParams)";

                $e = new \RuntimeException($message, $e->getCode(), $e->getPrevious());
            }

            if (!$this->isOpen()) {
                $this->flushInProgress = false;
                \XLite\Logger::getInstance()->registerException($e);
            }

            throw $e;
        }

        $this->tryExecuteAfterFlushCallbacks();
    }

    public function flushIsAvailable(): bool
    {
        return $this->isOpen() && !$this->flushInProgress;
    }
}
