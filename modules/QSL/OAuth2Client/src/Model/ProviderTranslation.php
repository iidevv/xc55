<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OAuth2Client\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Product multilingual data
 *
 * @ORM\Entity
 *
 * @ORM\Table (name="qsl_oauth2_client_provider_translations",
 *         indexes={
 *              @ORM\Index (name="ci", columns={"code","id"}),
 *              @ORM\Index (name="id", columns={"id"})
 *         }
 * )
 */
class ProviderTranslation extends \XLite\Model\Base\Translation
{
    /**
     * Name
     *
     * @var string
     *
     * @ORM\Column (type="string")
     */
    protected $name;

    /**
     * Link name
     *
     * @var string
     *
     * @ORM\Column (type="string")
     */
    protected $linkName;

    /**
     * Tooltip
     *
     * @var string
     *
     * @ORM\Column (type="text")
     */
    protected $tooltip = '';

    /**
     * @var \QSL\OAuth2Client\Model\Provider
     *
     * @ORM\ManyToOne (targetEntity="QSL\OAuth2Client\Model\Provider", inversedBy="translations")
     * @ORM\JoinColumn (name="id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $owner;

    // {{{

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return static
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getLinkName()
    {
        return $this->linkName;
    }

    /**
     * @param string $linkName
     *
     * @return static
     */
    public function setLinkName($linkName)
    {
        $this->linkName = $linkName;

        return $this;
    }

    /**
     * @return string
     */
    public function getTooltip()
    {
        return $this->tooltip;
    }

    /**
     * @param string $tooltip
     *
     * @return static
     */
    public function setTooltip($tooltip)
    {
        $this->tooltip = $tooltip;

        return $this;
    }

    // ]}}
}
