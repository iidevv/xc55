<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Role
 *
 * @ORM\Entity
 * @ORM\Table  (name="roles")
 */
class Role extends \XLite\Model\Base\I18n
{
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
     * Permissions
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany (targetEntity="XLite\Model\Role\Permission", mappedBy="roles", cascade={"merge","detach","persist"})
     */
    protected $permissions;

    /**
     * Profiles
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany (targetEntity="XLite\Model\Profile", inversedBy="roles")
     * @ORM\JoinTable (
     *      name="profile_roles",
     *      joinColumns={@ORM\JoinColumn (name="role_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn (name="profile_id", referencedColumnName="profile_id", onDelete="CASCADE")}
     * )
     */
    protected $profiles;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="XLite\Model\RoleTranslation", mappedBy="owner", cascade={"all"})
     */
    protected $translations;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = [])
    {
        $this->permissions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->profiles    = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Get public name
     *
     * @return string
     */
    public function getPublicName()
    {
        return $this->getName();
    }

    /**
     * Check - specified permission is allowed or not
     *
     * @param string $code Permission code
     *
     * @return boolean
     */
    public function isPermissionAllowed($code)
    {
        $result = false;

        foreach ($this->getPermissions() as $permission) {
            if ($permission->isAllowed($code)) {
                $result = true;

                break;
            }
        }

        return $result;
    }

    /**
     * Check - specified permission (only one from list) is allowed
     *
     * @param string|array $code Permission code(s)
     *
     * @return boolean
     */
    public function isPermissionAllowedOr($code)
    {
        $result = false;

        $list = [];
        foreach (func_get_args() as $code) {
            if (is_array($code)) {
                foreach ($code as $permissionCode) {
                    $list[] = $permissionCode;
                }
            } else {
                $list[] = $code;
            }
        }

        foreach ($list as $code) {
            if ($this->isPermissionAllowed($code)) {
                $result = true;

                break;
            }
        }

        return $result;
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
     * Add permissions
     *
     * @param \XLite\Model\Role\Permission $permissions
     * @return Role
     */
    public function addPermissions(\XLite\Model\Role\Permission $permissions)
    {
        $this->permissions[] = $permissions;
        return $this;
    }

    /**
     * Get permissions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * Add profiles
     *
     * @param \XLite\Model\Profile $profiles
     * @return Role
     */
    public function addProfiles(\XLite\Model\Profile $profiles)
    {
        $this->profiles[] = $profiles;
        return $this;
    }

    /**
     * Get profiles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProfiles()
    {
        return $this->profiles;
    }
}
