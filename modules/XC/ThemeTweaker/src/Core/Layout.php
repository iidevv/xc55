<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\Core;

use XC\ThemeTweaker\Model\Template;
use XCart\Extender\Mapping\Extender;
use XC\ThemeTweaker\Controller\Admin\NotificationEditor;

/**
 * Layout manager
 * @Extender\Mixin
 */
class Layout extends \XLite\Core\Layout
{
    public const THEME_TWEAKER_TEMPLATES_CACHE_KEY = 'theme_tweaker_templates';

    public const THEME_TWEAKER_INTERFACES_ZONES = [
        \XLite::INTERFACE_WEB  => [
            \XLite::ZONE_CUSTOMER,
        ],
        \XLite::INTERFACE_MAIL => [
            \XLite::ZONE_CUSTOMER,
            \XLite::ZONE_ADMIN,
        ],
    ];

    private $tweakerTemplates;

    protected function getTweakerPaths()
    {
        if ($this->tweakerTemplates === null) {
            $cacheDriver = \XLite\Core\Cache::getInstance()->getDriver();

            if (!$list = $cacheDriver->fetch(static::THEME_TWEAKER_TEMPLATES_CACHE_KEY)) {
                try {
                    $templates = \XLite\Core\Database::getRepo('XC\ThemeTweaker\Model\Template')->findBy([
                        'enabled' => true,
                    ]);
                } catch (\Throwable $e) {
                    $templates = [];
                }

                $list = array_map(static function ($template) {
                    /** @var \XC\ThemeTweaker\Model\Template $template */
                    return $template->getTemplate();
                }, $templates);

                $cacheDriver->save(
                    static::THEME_TWEAKER_TEMPLATES_CACHE_KEY,
                    $list,
                    \XLite\Core\Task\Base\Periodic::INT_1_WEEK
                );
            }

            $this->tweakerTemplates = $list;
        }

        return $this->tweakerTemplates;
    }

    public function hasTweakerTemplate(string $templateName, $interface = null, $zone = null)
    {
        if ($interface && $zone) {
            $templateName = "{$interface}/{$zone}/$templateName";
        }

        return in_array(
            $templateName,
            $this->getTweakerPaths(),
            true
        );
    }

    public function getTweakerContent(string $templateName, $interface = null, $zone = null, $enabledOnly = true)
    {
        $params = [
            'template' => $templateName,
        ];

        if ($interface && $zone) {
            $params['template'] = "{$interface}/{$zone}/{$templateName}";
        }

        if ($enabledOnly) {
            $params['enabled'] = true;
        }

        $template = \XLite\Core\Database::getRepo('XC\ThemeTweaker\Model\Template')->findOneBy($params);

        return $template ? $template->getBody() : null;
    }

    public function getTweakerDate(string $name): int
    {
        $name = "{$this->getInterface()}/{$this->getZone()}/{$name}";
        /** @var  Template $template */
        $template = \XLite\Core\Database::getRepo('XC\ThemeTweaker\Model\Template')->findOneBy(['template' => $name]);

        return $template ? $template->getDate() : 0;
    }

    /**
     * @param $localPath
     * @param $interface
     * @param $zone
     *
     * @return string
     */
    public function getTweakerPathByLocalPath($localPath, $interface = null, $zone = null)
    {
        $localPath = static::TEMPLATES_PATH . LC_DS . $localPath;

        if (!$interface || !$zone) {
            [$interface, $zone] = $this->getInterfaceAndZoneByLocalPath($localPath);
        }

        foreach ($this->getSkinPaths($interface, $zone) as $path) {
            if (strpos($localPath, $path['name']) === 0) {
                $pathSkin  = $path['name'];
                $shortPath = substr($localPath, strpos($localPath, LC_DS, strlen($pathSkin)) + strlen(LC_DS));

                return "{$interface}/{$zone}/{$shortPath}";
            }
        }

        return '';
    }

    public function getInterfaceAndZoneByLocalPath($localPath)
    {
        $result = [null, null];

        foreach (static::THEME_TWEAKER_INTERFACES_ZONES as $interface => $interfaceZones) {
            foreach ($interfaceZones as $zone) {
                $paths = $this->getSkinPaths($interface, $zone);

                foreach ($paths as $path) {
                    if (strpos($localPath, $path['name']) === 0) {
                        $result = [$interface, $zone];
                        break;
                    }
                }

                if ($result) {
                    break;
                }
            }
        }

        return $result;
    }

    protected function isAdminSidebarFirstVisible()
    {
        return (!\XLite::getController() instanceof NotificationEditor)
            && parent::isAdminSidebarFirstVisible();
    }


    public function getResourceFullPath(string $shortPath, string $interface = null, string $zone = null): ?string
    {
        if ($interface && $zone) {
            $prefix = (strpos($shortPath, 'custom_web') === 0)
                ? 'custom_web'
                : $interface;

            $shortPath = str_replace($prefix . LC_DS . $zone . LC_DS, '', $shortPath);
        }

        return parent::getResourceFullPath($shortPath, $interface, $zone);
    }
}
