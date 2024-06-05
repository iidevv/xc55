<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Lock;

trait SynchronousTrait
{
    /**
     * @param callable $func
     * @param int      $tries
     *
     * @return mixed
     * @throws \Exception
     */
    public function retryOnException(callable $func, $tries = 3)
    {
        try {
            return $func($this);
        } catch (\Exception $e) {
            if ($tries > 1) {
                return $this->retryOnException($func, $tries - 1);
            }
            throw $e;
        }
    }

    /**
     * @param callable $func
     * @param          $key
     *
     * @return mixed
     * @throws \Exception
     */
    public function synchronize(callable $func, $key)
    {
        $fileName = LC_DIR_TMP . $key . '.lock';

        if (!$fp = fopen($fileName, 'wb')) {
            if ($fp = fopen($fileName, 'xb')) {
                chmod($fileName, 0444);
            } else {
                usleep(100); // Give some time for chmod() to complete
                $fp = fopen($fileName, 'wb');
            }
        }

        if (flock($fp, LOCK_EX)) {
            try {
                $return = $func($this);
            } catch (\Exception $e) {
                flock($fp, LOCK_UN);
                fclose($fp);
                unlink($fileName);
                throw $e;
            }

            flock($fp, LOCK_UN);
        } else {
            throw new \Exception('Could not acquire lock');
        }

        fclose($fp);
        unlink($fileName);

        return $return ?: true;
    }
}
