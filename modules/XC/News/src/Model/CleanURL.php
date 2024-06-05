<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\News\Model;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;

/**
 * CleanURL
 * @Extender\Mixin
 */
class CleanURL extends \XLite\Model\CleanURL
{
    /**
     * Relation to a product entity
     *
     * @var \XC\News\Model\NewsMessage
     *
     * @ORM\ManyToOne  (targetEntity="XC\News\Model\NewsMessage", inversedBy="cleanURLs")
     * @ORM\JoinColumn (name="news_message_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $newsMessage;

    /**
     * Set newsMessage
     *
     * @param \XC\News\Model\NewsMessage $newsMessage
     * @return CleanURL
     */
    public function setNewsMessage(\XC\News\Model\NewsMessage $newsMessage = null)
    {
        $this->newsMessage = $newsMessage;
        return $this;
    }

    /**
     * Get newsMessage
     *
     * @return \XC\News\Model\NewsMessage
     */
    public function getNewsMessage()
    {
        return $this->newsMessage;
    }
}
