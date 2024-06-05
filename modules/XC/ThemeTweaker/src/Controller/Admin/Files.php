<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\Controller\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * File upload controller
 * @Extender\Mixin
 */
class Files extends \XLite\Controller\Admin\Files
{
    public const FILTER_CONTENT_IMAGE = 'content_image';

    /**
     * Returns list of stored files, filtered by type.
     *
     * Accepts request params:
     * type: File filter key. Available values: 'content_image'.
     *
     * @return void
     */
    protected function doActionGetImageManagerList()
    {
        $type = \XLite\Core\Request::getInstance()->type ?: static::FILTER_CONTENT_IMAGE;

        $list = [];

        $handlers = $this->getFilterHandlers();

        if (in_array($type, array_keys($handlers))) {
            $list = call_user_func($handlers[$type]);
        }

        $this->set('silent', true);
        $this->setSuppressOutput(true);

        $this->displayJSON($list);
    }

    /**
     * Deletes an image from image manager. Note that its usages are not removed.
     *
     * Accepts request params:
     * src: Image source path string.
     * id: OPTIONAL Image unique identifier.
     * type: OPTIONAL Image entity type
     *
     * @return void
     */
    protected function doActionRemoveFromImageManager()
    {
        $src = \XLite\Core\Request::getInstance()->src;
        $id = \XLite\Core\Request::getInstance()->id;
        $type = \XLite\Core\Request::getInstance()->type ?: 'XLite\Model\Image\Content';

        if ($src) {
            if ($id) {
                $entity = \XLite\Core\Database::getRepo($type)->find($id);

                if ($entity) {
                    $entity->delete();
                    $response = [
                        'message' => 'Successfully deleted ' . $src . ' image',
                    ];
                } else {
                    $this->headerStatus(404);
                    $response = [
                        'message' => 'No image with identifier ' . $id . ' found in ' . $type,
                    ];
                }
            } else {
                $thumb_path = LC_DIR_ROOT . $src;
                $upload_path = preg_replace('/thumbs/', 'uploads', $thumb_path, 1);

                if (
                    \Includes\Utils\FileManager::deleteFile($thumb_path) &&
                    \Includes\Utils\FileManager::deleteFile($upload_path)
                ) {
                    $response = [
                        'message' => 'Successfully deleted ' . $src . ' image',
                    ];
                } else {
                    $this->headerStatus(404);
                    $response = [
                        'message' => 'No image found with ' . $src . ' path',
                    ];
                }
            }
        } else {
            $this->headerStatus(400);
            $response = [
                'message' => 'No src param specified',
            ];
        }

        $this->set('silent', true);
        $this->setSuppressOutput(true);

        $this->displayJSON($response);
    }

    /**
     * Returns possible response strategies.
     * Contains callables as array values.
     *
     * @return array
     */
    protected function getFilterHandlers()
    {
        return [
            static::FILTER_CONTENT_IMAGE => [$this, 'getContentImages'],
        ];
    }

    protected function getContentImages()
    {
        $images = \XLite\Core\Database::getRepo('XLite\Model\Image\Content')->findAll();

        return array_map(function ($item) {
            return [
                'url' => $this->preprocessUrl($item->getFrontURL()),
                'id' => $item->getUniqueIdentifier(),
                'type' => get_class($item),
            ];
        }, $images);
    }

    protected function preprocessUrl($url)
    {
        return '/' . str_replace(\XLite\Core\URLManager::getShopURL(), '', $url);
    }
}
