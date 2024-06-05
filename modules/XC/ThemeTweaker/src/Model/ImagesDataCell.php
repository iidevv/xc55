<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\Model;

/**
 * Dummy Images data cell
 */
class ImagesDataCell extends \XLite\Model\Base\Dump
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $imageUrl;

    /**
     * @return string
     */
    protected function getPath()
    {
        return $this->path;
    }

    /**
     * @return string
     */
    protected function getName()
    {
        return $this->name;
    }

    /**
     * @return boolean
     */
    public function isExists()
    {
        return !is_null($this->getId());
    }

    /**
     * @return string
     */
    public function getFrontURL()
    {
        return $this->imageUrl;
    }

    /**
     * @return mixed
     */
    public function getUniqueIdentifier()
    {
        return $this->path;
    }

    /**
     * @param integer $width  Width limit OPTIONAL
     * @param integer $height Height limit OPTIONAL
     * @param integer $basewidth Base Width OPTIONAL
     * @param integer $baseheight Base Height OPTIONAL
     *
     * @return array (new width, new height, URL)
     */
    public function getResizedURL($width = null, $height = null, $basewidth = null, $baseheight = null)
    {
        return [120, 'auto', $this->imageUrl, $this->imageUrl];
    }
}
