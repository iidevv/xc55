<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Role;

use Doctrine\ORM\Mapping as ORM;

/**
 * Permission
 *
 * @ORM\Entity
 * @ORM\Table (name="permissions")
 */
class Permission extends \XLite\Model\Base\I18n implements \XLite\Model\Base\IModuleRelatedEntity
{
    public const ROOT_ACCESS = 'root access';

    /**
     * ID
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Code
     *
     * @var string
     *
     * @ORM\Column (type="string", options={ "fixed": true }, length=32)
     */
    protected $code;

    /**
     * Section
     *
     * @var string
     *
     * @ORM\Column (type="string", length=128)
     */
    protected $section;

    /**
     * Roles
     *
     * @var \XLite\Model\Role
     *
     * @ORM\ManyToMany (targetEntity="XLite\Model\Role", inversedBy="permissions")
     * @ORM\JoinTable (
     *      name="role_permissions",
     *      joinColumns={@ORM\JoinColumn (name="permission_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn (name="role_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    protected $roles;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="XLite\Model\Role\PermissionTranslation", mappedBy="owner", cascade={"all"})
     */
    protected $translations;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected ?string $module;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = [])
    {
        $this->roles = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Get public name
     *
     * @return string
     */
    public function getPublicName()
    {
        return $this->getName() ?: $this->getCode();
    }

    /**
     * Use this method to check if the given permission code allows with the permission
     *
     * @param string $code Code
     *
     * @return boolean
     */
    public function isAllowed($code)
    {
        return in_array($this->getCode(), [static::ROOT_ACCESS, $code], true);
    }

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
     * Set code
     *
     * @param string $code
     * @return Permission
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set section
     *
     * @param string $section
     * @return Permission
     */
    public function setSection($section)
    {
        $this->section = $section;
        return $this;
    }

    /**
     * Get section
     *
     * @return string
     */
    public function getSection()
    {
        return $this->section;
    }

    /**
     * Add roles
     *
     * @param \XLite\Model\Role $roles
     * @return Permission
     */
    public function addRoles(\XLite\Model\Role $roles)
    {
        $this->roles[] = $roles;
        return $this;
    }

    /**
     * Get roles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRoles()
    {
        return $this->roles;
    }

    public function getModule(): ?string
    {
        return $this->module;
    }

    public function setModule(string $module): void
    {
        $this->module = $module;
    }

    // {{{ Translation Getters / setters

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->getTranslationField(__FUNCTION__);
    }

    /**
     * @param string $description
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setDescription($description)
    {
        return $this->setTranslationField(__FUNCTION__, $description);
    }

    // }}}
}
