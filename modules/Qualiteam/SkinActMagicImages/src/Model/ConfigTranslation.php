<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

// vim: set ts=4 sw=4 sts=4 et:

namespace Qualiteam\SkinActMagicImages\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Magic360 config multilingual data
 *
 * @ORM\Entity
 * @ORM\Table  (name="magic360_config_translations",
 *      indexes={
 *           @ORM\Index (name="ci", columns={"code","id"}),
 *           @ORM\Index (name="id", columns={"id"})
 *       }
 * )
 */
class ConfigTranslation extends \XLite\Model\Base\Translation
{
    /**
     * @var \Qualiteam\SkinActMagicImages\Model\Config
     *
     * @ORM\ManyToOne (targetEntity="Qualiteam\SkinActMagicImages\Model\Config", inversedBy="translations")
     * @ORM\JoinColumn (name="id", referencedColumnName="config_id", onDelete="CASCADE")
     */
    protected $owner;

    /**
     * Human-readable option name
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255)
     */
    protected $option_name;

    /**
     * Option comment
     *
     * @var string
     *
     * @ORM\Column (type="text")
     */
    protected $option_comment = '';
}
