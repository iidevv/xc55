<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Iframe content
 *
 * @ORM\Entity
 * @ORM\Table  (name="iframe_contents")
 */
class IframeContent extends \XLite\Model\AEntity
{
    /**
     * Unique id
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Form URL
     *
     * @var string
     *
     * @ORM\Column (type="string")
     */
    protected $url;

    /**
     * Form method
     *
     * @var string
     *
     * @ORM\Column (type="string", length=16)
     */
    protected $method = 'POST';

    /**
     * Form data
     *
     * @var array
     *
     * @ORM\Column (type="array")
     */
    protected $data = [];

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return IframeContent
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set method
     *
     * @param string $method
     * @return IframeContent
     */
    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    /**
     * Get method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Set data
     *
     * @param array $data
     * @return IframeContent
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}
