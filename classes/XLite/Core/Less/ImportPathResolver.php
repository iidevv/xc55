<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Less;

use XLite\Core\Layout;

class ImportPathResolver
{
    /**
     * @param string $filePath original less file path
     * @param string $entryDir result file directory
     *
     * @return array|null
     */
    public function getImportPathAndUri($filePath, $entryDir)
    {
        $abs = $this->normalizePath($filePath);

        if (
            ($short = $this->getResourceShortPath($abs))
            && $full = $this->getResourceFullPath($short)
        ) {
            return [
                $full,
                dirname(\Includes\Utils\FileManager::makeRelativePath(
                    $entryDir,
                    $full
                )),
            ];
        }

        return null;
    }

    /**
     * @param $path
     *
     * @return null|string
     */
    protected function getResourceShortPath($path)
    {
        foreach (Layout::getInstance()->getLessFilePaths() as $skinPath) {
            if (mb_strpos($path, $skinPath['fs']) === 0) {
                return mb_substr($path, mb_strlen($skinPath['fs']) + 1);
            }
        }

        return null;
    }

    /**
     * @param string $shortPath
     *
     * @return string
     */
    protected function getResourceFullPath($shortPath)
    {
        return Layout::getInstance()->getResourceFullPath($shortPath);
    }

    /**
     * @param string $filePath original less file path
     * @param string $entryDir result file directory
     *
     * @return null|array
     */
    public function getParentPathAndUri($filePath, $entryDir)
    {
        if (@[$owner, $short] = $this->getResourceOwnerPathAndShortPath($filePath)) {
            if ($full = $this->getResourceFullParentPathByOwner($short, $owner)) {
                return [
                    $full,
                    dirname(\Includes\Utils\FileManager::makeRelativePath(
                        $entryDir,
                        $full
                    ))
                ];
            }
        }

        return null;
    }

    /**
     * @param $path
     *
     * @return array|null
     */
    protected function getResourceOwnerPathAndShortPath($path)
    {
        foreach (Layout::getInstance()->getLessFilePaths() as $skinPath) {
            if (mb_strpos($path, $skinPath['fs']) === 0) {
                return [
                    $skinPath['fs'],
                    mb_substr($path, mb_strlen($skinPath['fs']) + 1),
                ];
            }
        }

        return null;
    }

    /**
     * @param $shortPath
     * @param $owner
     *
     * @return null|string
     */
    protected function getResourceFullParentPathByOwner($shortPath, $owner)
    {
        return $this->getExistingParentPathForInterface($shortPath, $owner);
    }

    /**
     * @param $shortPath
     * @param $owner
     *
     * @return null|string
     */
    protected function getExistingParentPathForInterface($shortPath, $owner)
    {
        $paths = array_reduce(
            Layout::getInstance()->getLessFilePaths(),
            static function ($carry, $item) use ($owner) {
                if (!is_null($carry)) {
                    $carry[] = $item['fs'];
                } elseif (is_null($owner)) {
                    $carry = [$item['fs']];
                } elseif ($item['fs'] === $owner) {
                    return [];
                }

                return $carry;
            }
        );

        foreach ($paths as $skinPath) {
            if (file_exists($skinPath . DIRECTORY_SEPARATOR . $shortPath)) {
                return $skinPath . DIRECTORY_SEPARATOR . $shortPath;
            }
        }

        return null;
    }

    /**
     * @param $path
     *
     * @return string
     */
    protected function normalizePath($path)
    {
        $path = str_replace('\\', '/', $path);
        $path = preg_replace('#/+#', '/', $path);
        $data = explode('/', $path);
        $result = [];

        foreach ($data as $i => $v) {
            if (
                $v === '..'
                && count($result)
                && !in_array(end($result), ['..', '.'])
            ) {
                @array_pop($result);
            } else {
                $result[] = $v;
            }
        }

        return implode('/', $result);
    }
}
