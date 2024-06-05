<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// vim: set ts=4 sw=4 sts=4 et:

namespace Qualiteam\SkinActMagicImages\View;

use Qualiteam\SkinActMagicImages\Traits\MagicImagesTrait;

/**
 * File uploader
 */
class FileUploader extends \XLite\View\FileUploader
{
    use MagicImagesTrait;

    /**
     * Register files from common repository
     *
     * @return array
     */
    public function getCommonFiles()
    {
        $list = parent::getCommonFiles();

        if (isset($list[static::RESOURCE_JS])) {
            $key = array_search('file_uploader/controller.js', $list[static::RESOURCE_JS]);
            if ($key !== false) {
                unset($list[static::RESOURCE_JS][$key]);
            }
        }

        return $list;
    }

    /**
     * Get a list of JS files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $key = array_search('file_uploader/controller.js', $list);
        if ($key !== false) {
            unset($list[$key]);
        }
        $list[] = $this->getModulePath() . '/file_uploader/controller.js';

        return $list;
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = $this->getModulePath() . '/file_uploader/style.css';

        return $list;
    }

    /**
     * Return preview
     *
     * @return string
     */
    protected function getPreview()
    {
        $result = '';
        if ($this->getMessage()) {
            $result = '<i class="icon fa warning fa-exclamation-triangle"></i>';
        } elseif ($this->isImage() && $this->hasFile()) {
            $viewer = new \XLite\View\Image([
                'image'       => $this->getObject(),
                'maxWidth'    => $this->getParam(static::PARAM_MAX_WIDTH),
                'maxHeight'   => $this->getParam(static::PARAM_MAX_HEIGHT),
                'alt'         => '',
                'centerImage' => true,
            ]);

            $result = '<div class="preview">'
                . $viewer->getContent()
                . '</div>';
        } elseif ($this->isImage()) {
            $result = '<i class="icon fa fa-camera"></i>';
        }

        return $result;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getModulePath() . '/file_uploader/body.twig';
    }
}
