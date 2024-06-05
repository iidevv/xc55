<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoSocial\Model;

use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class ProductTranslation extends \XLite\Model\ProductTranslation
{
    /**
     * Custom Open graph meta tags
     *
     * @var string
     *
     * @ORM\Column (type="text", nullable=true)
     */
    protected $ogMeta = '';

    /**
     * Return OgMeta
     *
     * @return string
     */
    public function getOgMeta()
    {
        return $this->ogMeta;
    }

    /**
     * Set OgMeta
     *
     * @param string $ogMeta
     *
     * @return $this
     */
    public function setOgMeta($ogMeta)
    {
        $this->ogMeta = $ogMeta;
        return $this;
    }
}
