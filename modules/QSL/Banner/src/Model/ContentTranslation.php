<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Banner\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Banner image
 *
 * @ORM\Entity
 * @ORM\Table  (name="banner_contents_translations",
 *    indexes={
 *      @ORM\Index (name="ci", columns={"code","id"}),
 *      @ORM\Index (name="id", columns={"id"})
 *   }
 * )
 */
class ContentTranslation extends \XLite\Model\Base\Translation
{
    /**
     * HTML code
     *
     * @var   string
     *
     * @ORM\Column (type="text")
     */
    protected $content = '';

    /**
     * @var \QSL\Banner\Model\Content
     *
     * @ORM\ManyToOne (targetEntity="QSL\Banner\Model\Content", inversedBy="translations")
     * @ORM\JoinColumn (name="id", referencedColumnName="content_id", onDelete="CASCADE")
     */
    protected $owner;
}
