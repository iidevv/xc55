<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActMagicImages\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Magic360 config multilingual data
 *
 * @ORM\Entity
 * @ORM\Table  (name="magic_swatches_set_translations",
 *      indexes={
 *           @ORM\Index (name="ci", columns={"code","id"}),
 *           @ORM\Index (name="id", columns={"id"})
 *       }
 * )
 */
class MagicSwatchesSetTranslation extends \XLite\Model\Base\Translation
{
    /**
     * @var \Qualiteam\SkinActMagicImages\Model\MagicSwatchesSet
     *
     * @ORM\ManyToOne (targetEntity="Qualiteam\SkinActMagicImages\Model\MagicSwatchesSet", inversedBy="translations")
     * @ORM\JoinColumn (name="id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $owner;

    /**
     * @var string
     *
     * @ORM\Column (type="string", length=255)
     */
    protected $name;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }
}