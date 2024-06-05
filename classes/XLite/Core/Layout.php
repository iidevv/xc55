<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

use Includes\Utils\Module\Module;
use XLite\Core\Cache\ExecuteCachedTrait;
use XLite\Core\Lock\FileLock;

/**
 * Layout manager
 */
class Layout extends \XLite\Base\Singleton
{
    use ExecuteCachedTrait;

    public const TEMPLATES_PATH = 'templates';
    public const ASSETS_PATH    = 'assets';
    public const PATH_PATTERN   = '{{type}}';

    /**
     * Web URL output types
     */
    public const WEB_PATH_OUTPUT_SHORT = 'short';
    public const WEB_PATH_OUTPUT_FULL  = 'full';
    public const WEB_PATH_OUTPUT_URL   = 'url';

    /**
     * Layout style
     */
    public const LAYOUT_TWO_COLUMNS_LEFT  = 'left';
    public const LAYOUT_TWO_COLUMNS_RIGHT = 'right';
    public const LAYOUT_THREE_COLUMNS     = 'three';
    public const LAYOUT_ONE_COLUMN        = 'one';

    /**
     * Layout groups
     */
    public const LAYOUT_GROUP_DEFAULT = 'default';
    public const LAYOUT_GROUP_HOME    = 'home';

    public const SIDEBAR_STATE_FIRST_EMPTY            = 1;
    public const SIDEBAR_STATE_SECOND_EMPTY           = 2;
    public const SIDEBAR_STATE_FIRST_ONLY_CATEGORIES  = 4;
    public const SIDEBAR_STATE_SECOND_ONLY_CATEGORIES = 8;

    public const INITIALIZE_LESS = 'bootstrap/css/initialize.less';
    public const MERGE_ROOT      = 'root';

    /**
     * Widgets resources collector
     *
     * @var array
     */
    protected $resources = [];

    /**
     * Prepare resources flag
     *
     * @var boolean
     */
    protected $prepareResourcesFlag = false;

    /**
     * Current locale
     *
     * @var string
     */
    protected $locale = 'en';

    /**
     * Current interface
     *
     * @var string
     */
    protected $interface = \XLite::INTERFACE_WEB;

    /**
     * Current zone
     *
     * @var string
     */
    protected $zone = \XLite::ZONE_CUSTOMER;

    /**
     * Current resources group (on moment of registerResources())
     *
     * @var string
     */
    protected $currentGroup = null;

    /**
     * @var array
     */
    protected array $skinModel = [];

    /**
     * Resources cache
     *
     * @var array
     */
    protected $resourcesCache = [];

    /**
     * Skins cache flag
     *
     * @var boolean
     */
    protected $skinsCache = false;

    /**
     * Registered meta tags
     *
     * @var array
     */
    protected $metaTags = [];

    /**
     * Registered id strings
     *
     * @var array
     */
    protected $idStrings = [];

    /**
     * @var integer
     */
    protected $sidebarState = 0;

    /**
     * Get logo
     *
     * @return string
     */
    public function getLogo()
    {
        $color = $this->getLayoutColor();

        $image = 'images/logo' . ($color ? ('_' . $color) : '') . '.png';
        $url   = $this->getResourceWebPath($image, static::WEB_PATH_OUTPUT_URL, \XLite::INTERFACE_WEB, \XLite::ZONE_CUSTOMER);

        $url = $url
            ?: $this->getResourceWebPath('images/logo.png', static::WEB_PATH_OUTPUT_URL, \XLite::INTERFACE_WEB, \XLite::ZONE_CUSTOMER);

        $webDir = \Includes\Utils\ConfigParser::getOptions(['host_details', 'web_dir']) . '/';
        if (strpos($url, $webDir) === 0) {
            $url = substr($url, strlen($webDir));
        }

        return $url;
    }

    /**
     * Get logo
     *
     * @return string
     */
    public function getMobileLogo()
    {
        $color = $this->getLayoutColor();

        $image = 'images/mobile_logo' . ($color ? ('_' . $color) : '') . '.png';
        $url   = $this->getResourceWebPath($image, static::WEB_PATH_OUTPUT_URL, \XLite::INTERFACE_WEB, \XLite::ZONE_CUSTOMER);

        if (!$url) {
            $url = $this->getResourceWebPath('images/mobile_logo.png', static::WEB_PATH_OUTPUT_URL, \XLite::INTERFACE_WEB, \XLite::ZONE_CUSTOMER);
        }

        $webDir = \Includes\Utils\ConfigParser::getOptions(['host_details', 'web_dir']) . '/';
        if (strpos($url, $webDir) === 0) {
            $url = substr($url, strlen($webDir));
        }

        return $url;
    }

    /**
     * Get logo alt
     *
     * @return string
     */
    public function getLogoAlt()
    {
        return static::t('Logo alt');
    }

    /**
     * Get logo to invoice
     *
     * @return string
     */
    public function getInvoiceLogo()
    {
        $imageSizes       = \XLite\Logic\ImageResize\Generator::defineImageSizes();
        $invoiceLogoSizes = $imageSizes['XLite\Model\Image\Common\Logo']['Invoice'];
        $logoUrl          = $this->getLogo();

        $publicPrefix = strpos($logoUrl, 'public/') !== 0 ? 'public/' : '';
        $partUrl = $publicPrefix . $logoUrl;

        $url  = 'var/images/logo/' . implode('.', $invoiceLogoSizes) . '/' . $partUrl;
        $path = LC_DIR_PUBLIC . $url;

        if (!file_exists($path)) {
            $logoImage = \XLite\Core\Database::getRepo('XLite\Model\Image\Common\Logo')->getLogo();
            $logoImage->prepareSizes();
        }

        if (file_exists($path)) {
            $url = (!empty($_ENV['XCART_PUBLIC_DIR']) ? 'public/' : '') . $url;
        } else {
            $url = $logoUrl;
        }

        switch ($this->interface) {
            case \XLite::INTERFACE_MAIL:
            case \XLite::INTERFACE_PDF:
                $publicPrefix = strpos($url, 'public/') !== 0 ? 'public/' : '';
                return $publicPrefix . $url;

            default:
                return URLManager::getShopURL($url);
        }
    }

    /**
     * Get apple icon
     *
     * @return string
     */
    public function getFavicon()
    {
        $url    = $this->getResourceWebPath('favicon.ico', static::WEB_PATH_OUTPUT_URL, \XLite::INTERFACE_WEB, \XLite::ZONE_ADMIN);
        $webDir = \Includes\Utils\ConfigParser::getOptions(['host_details', 'web_dir']) . '/';
        if (strpos($url, $webDir) === 0) {
            $url = substr($url, strlen($webDir));
        }

        return $url;
    }

    /**
     * Get apple icon
     *
     * @return string
     */
    public function getAppleIcon()
    {
        $color = $this->getLayoutColor();

        $image = 'images/icon192x192' . ($color ? ('_' . $color) : '') . '.png';
        $url   = $this->getResourceWebPath($image, static::WEB_PATH_OUTPUT_URL, \XLite::INTERFACE_WEB, \XLite::ZONE_COMMON);

        if (!$url) {
            $url = $this->getResourceWebPath('images/icon192x192.png', static::WEB_PATH_OUTPUT_URL, \XLite::INTERFACE_WEB, \XLite::ZONE_COMMON);
        }

        $webDir = \Includes\Utils\ConfigParser::getOptions(['host_details', 'web_dir']) . '/';
        if (strpos($url, $webDir) === 0) {
            $url = substr($url, strlen($webDir));
        }

        return $url;
    }

    // {{{ Common getters

    /**
     * Return unique-guaranteed string to be used as id attr.
     * Returns given string in case of the first call with such argument.
     * Any subsequent calls return as <string>_<prefix>
     *
     * @param string $id Given id string
     *
     * @return string
     */
    public function getUniqueIdFor($id)
    {
        $result   = $id;
        $iterator = 1;

        while (in_array($result, $this->idStrings, true)) {
            $result = $id . '_' . $iterator;
            $iterator++;
        }

        $this->idStrings[] = $result;

        return $result;
    }

    /**
     * Return current interface
     *
     * @return string
     */
    public function getInterface()
    {
        return $this->interface;
    }

    /**
     * @return string
     */
    public function getZone()
    {
        return $this->zone;
    }

    public function getCurrentLayoutPreset()
    {
        return $this->getLayoutType();
    }

    /**
     * Switches the layout type for the given layout group
     *
     * @param string $group
     * @param string $type
     *
     * @return bool
     */
    public function switchLayoutType($group, $type)
    {
        $group = $group ?: static::LAYOUT_GROUP_DEFAULT;

        $availableLayoutTypes = $this->getAvailableLayoutTypes();
        $groupAvailableTypes  = $availableLayoutTypes[$group] ?? [];

        if (in_array($type, $groupAvailableTypes, true)) {
            $group_suffix = ($group === static::LAYOUT_GROUP_DEFAULT ? '' : '_' . $group);

            \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption([
                'category' => 'Layout',
                'name'     => 'layout_type' . $group_suffix,
                'value'    => $type,
            ]);

            return true;
        }

        return false;
    }

    /**
     * Returns layout types
     *
     * @return array
     */
    public function getLayoutTypes()
    {
        return [
            static::LAYOUT_TWO_COLUMNS_LEFT,
            static::LAYOUT_TWO_COLUMNS_RIGHT,
            static::LAYOUT_THREE_COLUMNS,
            static::LAYOUT_ONE_COLUMN,
        ];
    }

    /**
     * Returns layout groups and their targets,
     *
     * Default layout group is omitted because it is applicable to any target
     * and will be considered as fallback.
     *
     * @return array
     */
    public function getLayoutGroupTargets()
    {
        return [
            static::LAYOUT_GROUP_HOME => [
                'main',
            ],
        ];
    }

    /**
     * Returns available layout types
     *
     * @return array
     */
    public function getAvailableLayoutTypes()
    {
        return Skin::getInstance()->getAvailableLayoutTypes();
    }

    /**
     * Returns layout types, defined in module
     *
     * @param \XLite\Module\AModule $module
     *
     * @return array
     */
    public function getModuleLayoutTypes($module)
    {
        $validTypes = $this->getLayoutTypes();
        $types      = $module->callModuleMethod('getLayoutTypes', []);

        if (count($types) > 0 && is_array(array_values($types)[0])) {
            array_walk($types, static function (&$group) use ($validTypes) {
                $group = array_intersect($group, $validTypes);
            });

            return $types;
        } else {
            return [static::LAYOUT_GROUP_DEFAULT => array_intersect($types, $validTypes)];
        }
    }

    /**
     * Returns current layout type
     *
     * @param string $group Layout group name (by default - current displayed group)
     *
     * @return string
     */
    public function getLayoutType($group = null)
    {
        $group               = $group ?: $this->getCurrentLayoutGroup();
        $layoutType          = $this->getLayoutTypeByGroup($group);
        $availableTypes      = $this->getAvailableLayoutTypes();
        $groupAvailableTypes = $availableTypes[$group] ?? [];

        return in_array($layoutType, $groupAvailableTypes, true)
            ? $layoutType
            : Config::getInstance()->Layout->layout_type;
    }

    /**
     * Returns configured layout type value
     *
     * @param string $group Layout group name
     *
     * @return string
     */
    public function getLayoutTypeByGroup($group)
    {
        $group = ($group == static::LAYOUT_GROUP_DEFAULT ? '' : '_' . $group);

        return Config::getInstance()->Layout->{'layout_type' . $group};
    }

    /**
     * Returns layout group type option name
     *
     * @param string $group Layout group name
     *
     * @return string
     */
    public function getLayoutTypeLabelByGroup($group)
    {
        $group = ($group == static::LAYOUT_GROUP_DEFAULT ? '' : '_' . $group);

        $option = \XLite\Core\Database::getRepo('XLite\Model\Config')
            ->findOneBy(['name' => 'layout_type' . $group, 'category' => 'Layout']);

        return $option ? $option->getOptionName() : '';
    }

    /**
     * @return array
     * @see        \XLite\Core\Skin::getAvailableLayoutColors()
     *             Returns available layout colors
     *
     * @deprecated 5.4.0 Skin-related methods moved to \XLite\Core\Skin
     */
    public function getAvailableLayoutColors()
    {
        return Skin::getInstance()->getAvailableLayoutColors();
    }

    /**
     * @return string
     * @see        \XLite\Core\Skin::getSkinColorId()
     *             Returns current layout type
     *
     * @deprecated 5.4.0 Skin-related methods moved to \XLite\Core\Skin
     */
    public function getLayoutColor()
    {
        return Skin::getInstance()->getSkinColorId();
    }

    /**
     * @return string
     * @see        \XLite\Core\Skin::getSkinDisplayName()
     *             Returns current layout type
     *
     * @deprecated 5.4.0 Skin-related methods moved to \XLite\Core\Skin
     */
    public function getLayoutColorName()
    {
        return Skin::getInstance()->getSkinDisplayName();
    }

    /**
     * @return string
     * @see        \XLite\Core\Skin::getCurrentLayoutPreview()
     *             Returns current skin + color + layout preview image
     *
     * @deprecated 5.4.0 Skin-related methods moved to \XLite\Core\Skin
     */
    public function getCurrentLayoutPreview()
    {
        return Skin::getInstance()->getCurrentLayoutPreview();
    }

    /**
     * @return \XLite\Model\ImageSettings[]
     * @see        \XLite\Core\Skin::getCurrentImagesSettings()
     *             Returns current skin image settings
     *
     * @deprecated 5.4.0 Skin-related methods moved to \XLite\Core\Skin
     */
    public function getCurrentImagesSettings()
    {
        return Skin::getInstance()->getCurrentImagesSettings();
    }

    /**
     * Check if cloud zoom enabled
     *
     * @return boolean
     */
    public function getCloudZoomEnabled()
    {
        return (bool) Config::getInstance()->Layout->cloud_zoom;
    }

    /**
     * Returns cloud zoom mode
     *
     * @return string
     */
    public function getCloudZoomMode()
    {
        return Config::getInstance()->Layout->cloud_zoom_mode ?: \XLite\View\FormField\Select\CloudZoomMode::MODE_INSIDE;
    }

    /**
     * Returns allowed cloud zoom modes
     *
     * @return array
     */
    public function getAllowedCloudZoomModes()
    {
        return [
            \XLite\View\FormField\Select\CloudZoomMode::MODE_INSIDE,
            \XLite\View\FormField\Select\CloudZoomMode::MODE_OUTSIDE,
        ];
    }

    /**
     * Set cloud zoom mode
     *
     * @return $this
     */
    public function setCloudZoomMode($mode)
    {
        $mode = in_array($mode, $this->getAllowedCloudZoomModes()) ? $mode : \XLite\View\FormField\Select\CloudZoomMode::MODE_INSIDE;

        \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption(
            [
                'category' => 'Layout',
                'name'     => 'cloud_zoom_mode',
                'value'    => $mode,
            ]
        );

        return $this;
    }

    /**
     * Check if cloud zoom supported by skin
     *
     * @return boolean
     */
    public function isCloudZoomAllowed()
    {
        $skin = Skin::getInstance()->getCurrentSkinModule();

        return $skin
            ? Module::callMainClassMethod($skin['id'], 'isUseCloudZoom')
            : true;
    }

    /**
     * Check if image lazy loading is supported by skin
     *
     * @return bool|mixed
     */
    public function isLazyLoadEnabled()
    {
        return $this->isSkinAllowsLazyLoad() && Config::getInstance()->Performance->use_lazy_load;
    }

    /**
     * Check if image lazy loading is supported by skin
     *
     * @return bool|mixed
     */
    public function isSkinAllowsLazyLoad()
    {
        return \Xlite\Core\Skin::getInstance()->isUseLazyLoad();
    }

    // }}}

    // {{{ Resource path

    /**
     * @param string $shortPath Short path
     *
     * @return string|null
     */
    public function getSkinPreviewWebPath(string $shortPath, $outputType = self::WEB_PATH_OUTPUT_SHORT): ?string
    {
        if (!isset($this->resourcesCache[$shortPath])) {
            if ($path = $this->prepareSkinPreviewPath($shortPath)) {
                $this->resourcesCache[$shortPath] = $path;
            }
        }

        $web = $this->resourcesCache[$shortPath]['web'] ?? '';
        $fs  = $this->resourcesCache[$shortPath]['fs'] ?? '';

        return $web || $fs
            ? $this->prepareResourceURL($web . $shortPath, $outputType)
            : null;
    }

    /**
     * @param string      $shortPath Short path
     * @param string|null $interface Interface OPTIONAL
     * @param string|null $zone      Zone OPTIONAL
     *
     * @return string|null
     */
    public function getResourceFullPath(string $shortPath, string $interface = null, string $zone = null): ?string
    {
        $interface = $interface ?: $this->interface;
        $zone      = $zone ?: $this->zone;
        $key       = $this->prepareResourceKey($shortPath, $interface, $zone);

        if (!isset($this->resourcesCache[$key])) {
            if ($path = $this->prepareResourcePath($shortPath, $interface, $zone)) {
                $this->resourcesCache[$key] = $path;
            }
        }

        return isset($this->resourcesCache[$key])
            ? $this->resourcesCache[$key]['fs'] . LC_DS . $shortPath
            : null;
    }

    /**
     * @param string      $shortPath  Short path
     * @param string      $outputType Output type OPTIONAL
     * @param string|null $interface  Interface code OPTIONAL
     * @param string|null $zone       Zone code OPTIONAL
     *
     * @return string|null
     */
    public function getResourceWebPath($shortPath, $outputType = self::WEB_PATH_OUTPUT_SHORT, $interface = null, $zone = null): ?string
    {
        $interface = $interface ?: $this->interface;
        $zone      = $zone ?: $this->zone;
        $key       = $interface . '.' . $zone . '.' . $shortPath;

        if (!isset($this->resourcesCache[$key])) {
            if ($path = $this->prepareResourcePath($shortPath, $interface, $zone)) {
                $this->resourcesCache[$key] = $path;
            }
        }

        $web = $this->resourcesCache[$key]['web'] ?? '';
        $fs  = $this->resourcesCache[$key]['fs'] ?? '';

        return $web || $fs
            ? $this->prepareResourceURL($web . '/' . $shortPath, $outputType)
            : null;
    }

    /**
     * @param string $shortPath
     * @param string $interface
     * @param string $zone
     *
     * @return array
     */
    protected function prepareResourcePath(string $shortPath, string $interface, string $zone): array
    {
        switch ($shortPath) {
            case (strpos($shortPath, '.less') !== false):
                $result = $this->prepareLessResourcePath($shortPath, $interface, $zone);
                break;

            case (strpos($shortPath, '.twig') !== false):
                $result = $this->prepareTemplateResourcePath($shortPath, $interface, $zone);
                break;

            default:
                $result = $this->prepareAssetResourcePath($shortPath, $interface, $zone);
        }

        return $result ?? [];
    }

    /**
     * @param string $shortPath
     * @param string $interface
     * @param string $zone
     *
     * @return array
     */
    protected function prepareTemplateResourcePath(string $shortPath, string $interface, string $zone): array
    {
        foreach ($this->getSkinPaths($interface, $zone) as $path) {
            if (file_exists($path['fs'] . LC_DS . $shortPath)) {
                $result = $path;
                break;
            }
        }

        return $result ?? [];
    }

    /**
     * @param string $shortPath
     * @param string $interface
     * @param string $zone
     *
     * @return array
     */
    protected function prepareAssetResourcePath(string $shortPath, string $interface, string $zone): array
    {
        foreach ($this->getAssetPaths($interface, $zone) as $path) {
            $fullPath = $path['fs'] . LC_DS . $shortPath;
            if (
                file_exists($fullPath)
                && strpos(realpath($fullPath), $path['fs']) === 0
            ) {
                $result = $path;
                break;
            }
        }

        return $result ?? [];
    }

    /**
     * @param string $shortPath
     * @param string $interface
     * @param string $zone
     *
     * @return array
     */
    protected function prepareLessResourcePath(string $shortPath, string $interface, string $zone): array
    {
        foreach ($this->getLessFilePaths($interface, $zone) as $path) {
            if (file_exists($path['fs'] . LC_DS . $shortPath)) {
                $result = $path;
                break;
            }
        }

        return $result ?? [];
    }

    /**
     * @param string $shortPath
     *
     * @return array
     */
    protected function prepareSkinPreviewPath(string $shortPath): array
    {
        foreach ($this->getSkinPreviewPaths() as $path) {
            if (file_exists($path['fs'] . LC_DS . $shortPath)) {
                $result = $path;
                break;
            }
        }

        return $result ?? [];
    }

    /**
     * @return array
     */
    public function getSkinPreviewPaths(): array
    {
        return $this->executeCachedRuntime(static function () {
            $webDir = \Includes\Utils\ConfigParser::getOptions(['host_details', 'web_dir']);

            $webDir        = !empty($webDir)
                ? ltrim($webDir, '/') . '/'
                : '';
            $webRootPrefix = $webDir . (!empty($_ENV['XCART_PUBLIC_DIR']) ? 'public/' : '');

            $paths[] = [
                'fs'  => LC_DIR_PUBLIC,
                'web' => $webRootPrefix,
            ];

            return $paths;
        }, [__CLASS__, __METHOD__]);
    }

    /**
     * Defines the resource cache unique identifier of the given resource
     *
     * @param string $shortPath Short path for resource
     * @param string $interface Interface of the resource
     * @param string $zone      Zone of the resource
     *
     * @return string Unique key identifier for the resource to be stored in the resource cache
     */
    protected function prepareResourceKey($shortPath, $interface, $zone)
    {
        return $this->interface . '.' . $this->zone . '.' . $interface . '.' . $zone . '.' . $shortPath;
    }

    // }}}

    /**
     * Prepare skin URL
     *
     * @param string $shortPath  Short path
     * @param string $outputType Output type OPTIONAL
     *
     * @return string
     * @deprecated
     */
    public function prepareSkinURL($shortPath, $outputType = self::WEB_PATH_OUTPUT_SHORT)
    {
        $skins = $this->getAssetPaths();
        $path  = array_pop($skins);

        return $this->prepareResourceURL($path['web'] . '/' . $shortPath, $outputType);
    }

    /**
     * Save substitutional skins data into cache
     *
     * @return void
     */
    public function saveSkins()
    {
        \XLite\Core\Database::getCacheDriver()->save(
            get_called_class() . '.SubstitutionalSkins',
            $this->resourcesCache
        );
    }

    /**
     * @param string|null $interface Interface code OPTIONAL
     * @param string|null $zone      Interface code OPTIONAL
     *
     * @return array
     */
    public function getSkinPaths(?string $interface = null, ?string $zone = null): array
    {
        $interface = $interface ?: $this->interface;
        $zone      = $zone ?: $this->zone;

        return $this->executeCachedRuntime(function () use ($interface, $zone) {
            foreach ($this->skinModel[$interface][$zone] ?? [] as $fsPath) {
                $fsPath = str_replace(static::PATH_PATTERN, static::TEMPLATES_PATH, $fsPath);

                $paths[] = [
                    'name' => $fsPath,
                    'fs'   => LC_DIR_ROOT . $fsPath,
                ];
            }

            return $paths ?? [];
        }, [__CLASS__, __METHOD__, $interface, $zone]);
    }

    /**
     * @param string|null $interface
     * @param string|null $zone
     *
     * @return array
     */
    public function getLessFilePaths(?string $interface = null, ?string $zone = null): array
    {
        $interface = $interface ?: $this->interface;
        $zone      = $zone ?: $this->zone;

        return $this->executeCachedRuntime(function () use ($interface, $zone) {
            foreach ($this->skinModel[$interface][$zone] ?? [] as $fsPath) {
                $replacement = strpos($fsPath, static::PATH_PATTERN) === 0
                    ? static::ASSETS_PATH
                    : 'public';

                $fsPath = str_replace(static::PATH_PATTERN, $replacement, $fsPath);

                $paths[] = [
                    'name' => $fsPath,
                    'fs'   => LC_DIR_ROOT . $fsPath,
                ];
            }

            return $paths ?? [];
        }, [__CLASS__, __METHOD__, $interface, $zone]);
    }

    /**
     * @param string|null $interface
     * @param string|null $zone
     *
     * @return array
     */
    public function getAssetPaths(?string $interface = null, ?string $zone = null): array
    {
        $interface = $interface ?: $this->interface;
        $zone      = $zone ?: $this->zone;

        return $this->executeCachedRuntime(static function () use ($interface, $zone) {
            $webDir = \Includes\Utils\ConfigParser::getOptions(['host_details', 'web_dir']);

            $webDir        = !empty($webDir)
                ? ltrim($webDir, '/') . '/'
                : '';
            $webRootPrefix = $webDir . (!empty($_ENV['XCART_PUBLIC_DIR']) ? 'public/' : '');
            $fsPath        = static::ASSETS_PATH . LC_DS . $interface . LC_DS . $zone;

            $paths[] = [
                'name' => $fsPath,
                'fs'   => LC_DIR_PUBLIC . $fsPath,
                'web'  => $webRootPrefix . $fsPath,
            ];

            return $paths;
        }, [__CLASS__, __METHOD__, $interface, $zone]);
    }

    /**
     * Prepare resource URL
     *
     * @param string $url        URL
     * @param string $outputType Output type
     *
     * @return string
     */
    protected function prepareResourceURL($url, $outputType)
    {
        $url = trim($url);

        switch ($outputType) {
            case static::WEB_PATH_OUTPUT_FULL:
                if (preg_match('/^\w+\//S', $url)) {
                    $url = \XLite::getInstance()->getShopURL($url);
                }
                break;

            default:
        }

        return '/' . $url;
    }

    /**
     * Restore substitutional skins data from cache
     *
     * @return void
     */
    protected function restoreSkins()
    {
        $driver = \XLite\Core\Database::getCacheDriver();

        $data = $driver
            ? $driver->fetch(get_called_class() . '.SubstitutionalSkins')
            : null;

        if ($data && is_array($data)) {
            $this->resourcesCache = $data;
        }
    }

    // }}}

    // {{{ Initialization routines

    public function setInterfaceZone(string $interface = \XLite::INTERFACE_WEB, string $zone = \XLite::ZONE_CUSTOMER)
    {
        $this->prepareResources();

        $this->interface = $interface;
        $this->zone      = $zone;
    }

    public function callInInterfaceZone(callable $fn, string $interface = \XLite::INTERFACE_WEB, string $zone = \XLite::ZONE_CUSTOMER)
    {
        $currentInterface = $this->interface;
        $currentZone      = $this->zone;

        $this->setInterfaceZone($interface, $zone);

        $result = $fn();

        $this->setInterfaceZone($currentInterface, $currentZone);

        return $result;
    }

    /**
     * Set current skin as the mail one
     *
     * @param string $zone
     *
     * @return void
     * @deprecated
     */
    public function setMailSkin($zone = \XLite::ZONE_CUSTOMER)
    {
        $this->prepareResources();

        $this->interface = \XLite::INTERFACE_MAIL;
        $this->zone      = $zone;
    }

    /**
     * Constructor
     */
    protected function __construct()
    {
        parent::__construct();

        $this->skinsCache = (bool) \Includes\Utils\ConfigParser::getOptions(['performance', 'skins_cache']);

        if ($this->skinsCache) {
            $this->restoreSkins();
            register_shutdown_function([$this, 'saveSkins']);
        }

        $this->skinModel = \XCart\Container::getContainer()->getParameter('xcart.skin_model');
    }

    // }}}

    // {{{ Resources

    /**
     * Register resources
     *
     * @param array   $resources Resources
     * @param integer $index     Index (weight)
     * @param string  $interface Interface OPTIONAL
     * @param string  $group     Group OPTIONAL
     *
     * @return void
     */
    public function registerResources(array $resources, $index, $interface = null, $zone = null, $group = null)
    {
        $this->currentGroup = $group;

        foreach ($resources as $type => $files) {
            $method = 'register' . strtoupper($type) . 'Resources';

            if (method_exists($this, $method)) {
                $this->{$method}($files, $index, $interface, $zone);
            }
        }

        $this->prepareResourcesFlag = false;
        $this->currentGroup         = null;
    }

    /**
     * Return list of all registered resources
     *
     * @return array
     */
    public function getRegisteredResources()
    {
        $result = [];
        foreach ($this->getResourcesTypes() as $type) {
            $result[$type] = $this->getRegisteredResourcesByType($type);
        }

        return $result;
    }

    /**
     * Get registered resources by type
     *
     * @param string $type Resource type
     *
     * @return array
     */
    public function getRegisteredResourcesByType($type)
    {
        $result = [];
        foreach ($this->getPreparedResources() as $subresources) {
            if (!empty($subresources[$type])) {
                foreach ($subresources[$type] as $path => $file) {
                    if (isset($result[$path])) {
                        unset($result[$path]);
                    }
                    $result[$path] = $file;
                }
            }
        }

        return $result;
    }

    /**
     * Return list of all registered and prepared resources
     *
     * @param string $group Filter by group OPTIONAL
     *
     * @return array
     */
    public function getRegisteredPreparedResources($group = null)
    {
        $result = [];
        foreach ($this->getResourcesTypes() as $type) {
            $resources = $this->getPreparedResourcesByType($type);

            if ($group) {
                $resources = array_filter(
                    $resources,
                    static function ($item) use ($group) {
                        return isset($item['group']) && $item['group'] == $group;
                    }
                );
            }

            $result[$type] = $resources;
        }

        return $result;
    }

    /**
     * Get registered and prepared resources by type
     *
     * @param string $type Resource type
     *
     * @return array
     */
    public function getPreparedResourcesByType($type)
    {
        $resources = array_filter(
            \XLite\Core\Layout::getInstance()->getRegisteredResourcesByType($type),
            [$this, 'isValid' . strtoupper($type) . 'Resource']
        );

        $method = 'prepare' . strtoupper($type) . 'Resources';

        return $this->$method($resources);
    }

    /**
     * Get resources types
     *
     * @return array
     */
    public function getResourcesTypes()
    {
        return [
            \XLite\View\AView::RESOURCE_JS,
            \XLite\View\AView::RESOURCE_CSS,
        ];
    }

    /**
     * Get prepared resources
     *
     * @return array
     */
    protected function getPreparedResources()
    {
        $this->prepareResources();

        return $this->resources;
    }

    /**
     * Prepare resources
     *
     * @param array $resources Resources
     *
     * @return array
     */
    protected function prepareResources()
    {
        if (!$this->prepareResourcesFlag) {
            ksort($this->resources, SORT_NUMERIC);

            foreach ($this->resources as $index => $subresources) {
                foreach ($subresources as $type => $files) {
                    foreach ($files as $name => $file) {
                        $file = $this->prepareResource($file, $type);
                        if ($file) {
                            $files[$name] = $file;
                        } else {
                            unset($files[$name]);
                        }
                    }

                    if ($files) {
                        $subresources[$type] = $files;
                    } else {
                        unset($subresources[$type]);
                    }
                }

                if ($subresources) {
                    $this->resources[$index] = $subresources;
                } else {
                    unset($this->resources[$index]);
                }
            }

            $this->prepareResourcesFlag = true;
        }
    }

    /**
     * Prepare resource
     *
     * @param array  $data Resource data
     * @param string $type Resource type
     *
     * @return array
     */
    protected function prepareResource(array $data, $type)
    {
        $data = $this->prepareResourceFullURL($data, $type);
        if ($data) {
            $method = 'prepareResource' . strtoupper($type);
            if (method_exists($this, $method)) {
                $data = $this->$method($data, $type);
            }
        }

        return $data;
    }

    /**
     * Prepare resource full URL
     *
     * @param array  $data Resource data
     * @param string $type Resource type
     *
     * @return array
     */
    protected function prepareResourceFullURL(array $data, $type)
    {
        if (empty($data['url'])) {
            foreach ($data['filelist'] as $file) {
                $shortURL = str_replace(LC_DS, '/', $file);

                $fullURL = $this->getResourceWebPath(
                    $shortURL,
                    \XLite\Core\Layout::WEB_PATH_OUTPUT_URL,
                    $data['interface'],
                    $data['zone']
                );

                if ($fullURL !== null) {
                    $data['original'] = $data['file'];
                    $data['file']     = $this->getResourceFullPath($shortURL, $data['interface'], $data['zone']);
                    $data             += [
                        'media' => 'all',
                        'url'   => $fullURL,
                    ];

                    break;
                }
            }
        }

        return empty($data['url']) ? null : $data;
    }

    /**
     * @return integer
     */
    public function getSidebarState()
    {
        return $this->sidebarState;
    }

    /**
     * @param integer $sidebarState
     */
    public function setSidebarState($sidebarState)
    {
        $this->sidebarState = $sidebarState;
    }

    /**
     * Prepare resource as CSS
     *
     * @param array  $data Resource data
     * @param string $type Resource type
     *
     * @return array
     */
    protected function prepareResourceCSS(array $data, $type)
    {
        if ($this->isLESSResource($data)) {
            $data = $this->prepareResourceLESS($data);
        }

        return $data;
    }

    /**
     * Check if the resource is a LESS one
     *
     * @param array $data Resource data
     *
     * @return boolean
     */
    protected function isLESSResource(array $data)
    {
        return !empty($data['file']) && preg_match('/\.less$/S', $data['file']);
    }

    /**
     * Prepare resource as LESS
     *
     * @param array $data Resource data
     *
     * @return array
     */
    protected function prepareResourceLESS($data)
    {
        $data['less'] = true;

        return $data;
    }

    /**
     * Prepare resource as JS
     *
     * @param array  $data Resource data
     * @param string $type Resource type
     *
     * @return array
     */
    protected function prepareResourceJS(array $data, $type)
    {
        return $data;
    }

    /**
     * Prepare CSS resources
     *
     * @param array $resources Resources
     *
     * @return boolean
     */
    protected function isValidCSSResource(array $resources)
    {
        return isset($resources['url']);
    }

    /**
     * Prepare JS resources
     *
     * @param array $resources Resources
     *
     * @return boolean
     */
    protected function isValidJSResource(array $resources)
    {
        return isset($resources['url']);
    }

    /**
     * Prepare CSS resources
     *
     * @param array $resources Resources
     *
     * @return array
     */
    protected function prepareCSSResources(array $resources)
    {
        $lessResources = [];

        // Detect the merged resources grouping
        foreach ($resources as $index => $resource) {
            if (isset($resource['less'])) {
                if ($resource['media'] === 'force_all') {
                    $resources[$index]['media'] = 'all';
                } elseif (
                    !isset($resource['merge'])
                    && $resource['zone'] !== \XLite::ZONE_COMMON
                ) {
                    $resource['merge'] = static::INITIALIZE_LESS;
                }

                if (isset($resource['merge']) && $resource['merge'] !== static::MERGE_ROOT) {
                    $lessResources[$resource['merge']][] = $resource;
                    unset($resources[$index]);
                }
            }
        }

        foreach ($resources as $index => $resource) {
            if (isset($resource['less'])) {
                if (!isset($lessResources[$resource['original']])) {
                    // one resource group is registered
                    $lessGroup = [$resource];
                } else {
                    // The resource is placed into the head of the less resources list
                    $lessGroup = array_merge([$resource], $lessResources[$resource['original']]);
                }

                if (Request::getInstance()->isAJAX()) {
                    if (FileLock::getInstance()->isRunning('cssMaking_' . $index)) {
                        continue;
                    }
                    FileLock::getInstance()->setRunning('cssMaking_' . $index);
                }

                $resources[$index] = \XLite\Core\LessParser::getInstance()->makeCSS($lessGroup);

                // Media type is derived from the parent resource
                $resources[$index]['media'] = $resource['media'];

                if (Request::getInstance()->isAJAX()) {
                    FileLock::getInstance()->release('cssMaking_' . $index);
                }
            }
        }

        return $resources;
    }

    /**
     * Prepare JS resources
     *
     * @param array $resources Resources
     *
     * @return array
     */
    protected function prepareJSResources(array $resources)
    {
        return $resources;
    }

    /**
     * Main JS resources registrator. see self::registerResources() for more info
     *
     * @param array   $files     List of file relative pathes to the resources
     * @param integer $index     Position in the ordered resources queue
     * @param string  $interface Interface where the files are located
     * @param string  $zone
     *
     * @see \XLite\View\AView::registerResources()
     */
    protected function registerJSResources(array $files, $index, $interface, $zone)
    {
        $this->registerResourcesByType($files, $index, $interface, $zone, \XLite\View\AView::RESOURCE_JS);
    }

    /**
     * Main CSS resources registrator. see self::registerResources() for more info
     *
     * @param array   $files     List of file relative pathes to the resources
     * @param integer $index     Position in the ordered resources queue
     * @param string  $interface Interface where the files are located
     * @param string  $zone
     *
     * @see \XLite\View\AView::registerResources()
     */
    protected function registerCSSResources(array $files, $index, $interface, $zone)
    {
        $this->registerResourcesByType($files, $index, $interface, $zone, \XLite\View\AView::RESOURCE_CSS);
    }

    /**
     * Main common registrator of resources. see self::registerResources() for more info
     * This method takes the files list and registers them as the resources of the provided $type
     *
     * @param array   $files     List of file relative pathes to the resources
     * @param integer $index     Position in the ordered resources queue
     * @param string  $interface Interface where the files are located
     * @param string  $zone
     * @param string  $type      Type of the resources ('js', 'css')
     *
     */
    protected function registerResourcesByType(array $files, $index, $interface, $zone, $type)
    {
        foreach ($files as $resource) {
            $resource = $this->prepareResourceByType($resource, $index, $interface, $zone, $type);
            $hash     = md5(serialize($resource));

            if ($resource && $this->currentGroup && !isset($resource['group'])) {
                $resource['group'] = $this->currentGroup;
            }

            if ($resource && !isset($this->resources[$index][$type][$hash])) {
                $this->resources[$index][$type][$hash] = $resource;
            }
        }
    }

    /**
     * The resource must be prepared before the registration in the resources storage:
     * - the file must be correctly located and full file path must be found
     * - the web location of the resource must be found
     *
     * Then this method actually stores the resource into the static resources storage
     *
     * @param string|array|null $resource Resource file path or array of resources
     * @param integer           $index
     * @param string            $interface
     * @param string            $zone
     * @param string            $type
     *
     * @return array
     */
    protected function prepareResourceByType($resource, $index, $interface, $zone, $type)
    {
        if (empty($resource)) {
            $resource = null;
        } elseif (is_string($resource)) {
            $resource = [
                'file'     => $resource,
                'filelist' => [$resource],
            ];
        }

        if ($resource && !isset($resource['url'])) {
            if (!isset($resource['filelist'])) {
                $resource['filelist'] = [$resource['file']];
            }

            if (!isset($resource['interface'])) {
                $resource['interface'] = $interface;
            }

            if (!isset($resource['zone'])) {
                $resource['zone'] = $zone;
            }
        }

        return $resource;
    }

    // }}}

    // {{{ Meta tags

    /**
     * Register meta tags to include in page content
     *
     * @param array $metaTags
     */
    public function registerMetaTags(array $metaTags)
    {
        if (!empty($metaTags)) {
            $this->metaTags = array_unique(array_merge($this->metaTags, $metaTags));
        }
    }

    /**
     * Return list of all registered meta tags
     *
     * @return array
     */
    public function getRegisteredMetaTags()
    {
        return $this->metaTags;
    }

    // }}}

    // {{{ Sidebars

    /**
     * Returns current layout group based on best-first target
     * @return string
     */
    public function getCurrentLayoutGroup()
    {
        $target = \XLite\Core\Request::getInstance()->target;
        $groups = $this->getLayoutGroupTargets();

        $current = static::LAYOUT_GROUP_DEFAULT;

        foreach ($groups as $name => $targets) {
            if (in_array($target, $targets, true)) {
                $current = $name;
                break;
            }
        }

        return $current;
    }

    /**
     * Is Sidebar Single
     *
     * @return boolean
     */
    public function isSidebarSingle()
    {
        return in_array(
            $this->getLayoutType(),
            [
                static::LAYOUT_TWO_COLUMNS_LEFT,
                static::LAYOUT_TWO_COLUMNS_RIGHT,
            ],
            true
        );
    }

    /**
     * Check - first sidebar is visible or not
     *
     * @return boolean
     */
    public function isSidebarFirstVisible()
    {
        return \XLite::isAdminZone()
            ? $this->isAdminSidebarFirstVisible()
            : $this->isCustomerSidebarFirstVisible();
    }

    /**
     * Check - second sidebar is visible or not
     *
     * @return boolean
     */
    public function isSidebarSecondVisible()
    {
        return \XLite::isAdminZone()
            ? $this->isAdminSidebarSecondVisible()
            : $this->isCustomerSidebarSecondVisible();
    }

    /**
     * Check - first sidebar is visible or not (in admin interface)
     *
     * @return boolean
     */
    protected function isAdminSidebarFirstVisible()
    {
        $widget = new \Xlite\View\Controller();

        return $widget->isViewListVisible('admin.main.page.content.left')
            && !\Xlite::getController()->isForceChangePassword();
    }

    /**
     * Check - second sidebar is visible or not (in admin interface)
     *
     * @return boolean
     */
    protected function isAdminSidebarSecondVisible()
    {
        return false;
    }

    /**
     * Check - first sidebar is visible or not (in customer interface)
     *
     * @return boolean
     */
    protected function isCustomerSidebarFirstVisible()
    {
        return in_array(
            $this->getLayoutType(),
            [
                static::LAYOUT_TWO_COLUMNS_LEFT,
                static::LAYOUT_THREE_COLUMNS,
            ],
            true
        )
        && !in_array(
            \XLite\Core\Request::getInstance()->target,
            $this->getSidebarFirstHiddenTargets(),
            true
        );
    }

    /**
     * Check - second sidebar is visible or not (in customer interface)
     *
     * @return boolean
     */
    protected function isCustomerSidebarSecondVisible()
    {
        return in_array(
            $this->getLayoutType(),
            [
                \XLite\Core\Layout::LAYOUT_TWO_COLUMNS_RIGHT,
                \XLite\Core\Layout::LAYOUT_THREE_COLUMNS,
            ],
            true
        )
        && !in_array(
            \XLite\Core\Request::getInstance()->target,
            $this->getSidebarSecondHiddenTargets(),
            true
        );
    }

    /**
     * Define the pages where first sidebar will be hidden.
     * By default we hide it on:
     *      product page,
     *      cart page,
     *      checkout page
     *      checkout success (invoice) page
     *      payment page
     *
     * @return array
     */
    protected function getSidebarFirstHiddenTargets()
    {
        return [
            'cart',
            'product',
            'checkout',
            'checkoutPayment',
            'checkoutSuccess',
        ];
    }

    /**
     * Define the pages where second sidebar will be hidden.
     * By default we hide it on:
     *      product page,
     *      cart page,
     *      checkout page
     *      checkout success (invoice) page
     *      payment page
     *
     * @return array
     */
    protected function getSidebarSecondHiddenTargets()
    {
        return [
            'cart',
            'product',
            'checkout',
            'checkoutPayment',
            'checkoutSuccess',
        ];
    }

    // }}}
}
