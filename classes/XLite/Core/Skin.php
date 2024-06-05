<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

use Includes\Utils\Module\Manager;
use Includes\Utils\Module\Module;
use XLite\Core\Cache\ExecuteCachedTrait;

/**
 * Class Skin
 * @package XLite\Core
 *
 * @method isUseCloudZoom
 * @method isUseLazyLoad
 */
class Skin extends \XLite\Base\Singleton
{
    use ExecuteCachedTrait;

    /**
     * Default skin id
     */
    public const SKIN_STANDARD = 'standard';
    public const COLOR_DEFAULT = 'Default';

    /**
     * Proxies the call to the current skin Main class. Always returns null if current skin is the default.
     *
     * @return mixed
     */
    public function __call(string $name, array $arguments = [])
    {
        $module = $this->getCurrentSkinModule();

        return isset($module['id'])
            ? Module::callMainClassMethod($module['id'], $name, $arguments)
            : null;
    }

    public function getCurrentSkinModule(): array
    {
        return $this->executeCachedRuntime(static function () {
            foreach (Manager::getRegistry()->getModules() as $module) {
                if (isset($module['isActiveSkin']) && $module['isActiveSkin']) {
                    return $module;
                }
            }

            return [];
        });
    }

    public function getSkinModuleName(array $module = []): string
    {
        if (!$module) {
            $module = $this->getCurrentSkinModule();
        }

        return $module['moduleName'] ?? $this->getDefaultSkinName();
    }

    public function getCurrentSkinModuleId(): string
    {
        return $this->getCurrentSkinModule()
            ? $this->getCurrentSkinModule()['id']
            : $this->getDefaultSkinModuleId();
    }

    public function getDefaultSkinModuleId(): string
    {
        return static::SKIN_STANDARD;
    }

    public function getDefaultSkinName(): string
    {
        return Translation::lbl('Standard');
    }

    /**
     * Returns available layout colors
     */
    public function getAvailableLayoutColors(array $module = []): array
    {
        $module = $module ?: $this->getCurrentSkinModule();

        if ($module && ($module['id'] !== $this->getDefaultSkinModuleId())) {
            $result = Module::callMainClassMethod($module['id'], 'getLayoutColors');
        }

        return $result ?? [];
    }

    /**
     * Returns layout types, defined in module
     */
    public function getAvailableLayoutTypes(): array
    {
        $module     = $this->getCurrentSkinModule();
        $validTypes = Layout::getInstance()->getLayoutTypes();

        if (!$module) {
            // default skin
            return [
                Layout::LAYOUT_GROUP_DEFAULT => $validTypes,
                Layout::LAYOUT_GROUP_HOME    => $validTypes,
            ];
        }

        $types = Module::callMainClassMethod($module['id'], 'getLayoutTypes', []);

        if (count($types) > 0 && is_array(array_values($types)[0])) {
            array_walk($types, static function (&$group) use ($validTypes) {
                $group = array_intersect($group, $validTypes);
            });

            return $types;
        }

        return [
            Layout::LAYOUT_GROUP_DEFAULT => array_intersect($types, $validTypes),
        ];
    }

    /**
     * Returns current skin color identifier
     */
    public function getSkinColorId(array $module = [], string $color = ''): string
    {
        $module          = $module ?: $this->getCurrentSkinModule();
        $layoutColor     = $color ?: Config::getInstance()->Layout->color;
        $availableColors = $this->getAvailableLayoutColors($module);

        if ($availableColors) {
            if (isset($availableColors[$layoutColor])) {
                return $layoutColor;
            }

            return array_keys($availableColors)[0];
        }

        return '';
    }

    /**
     * Returns current skin + color display name
     */
    public function getSkinDisplayName(array $module = [], string $color = ''): string
    {
        $module          = $module ?: $this->getCurrentSkinModule();
        $layoutColor     = $color ?: Config::getInstance()->Layout->color;
        $availableColors = $this->getAvailableLayoutColors($module);

        if ($availableColors) {
            if (isset($availableColors[$layoutColor])) {
                return $availableColors[$layoutColor];
            }

            return array_shift($availableColors);
        }

        return $this->getSkinModuleName($module);
    }

    /**
     * Returns skin layout preview image URL
     */
    public function getSkinPreview(array $module = [], string $color = '', string $type = ''): string
    {
        return $this->getSkinPreviewUrl('preview', $module, $color, $type);
    }

    /**
     * Returns skin module preview image URL
     */
    public function getSkinListItemPreview(array $module = [], string $color = '', string $type = ''): string
    {
        return $this->getSkinPreviewUrl('preview_list', $module, $color, $type);
    }

    /**
     * Returns current skin + color + layout preview image URL
     */
    public function getCurrentLayoutPreview(?string $group = null): string
    {
        return $this->getSkinPreview(
            $this->getCurrentSkinModule(),
            $this->getSkinColorId(),
            Layout::getInstance()->getLayoutType($group)
        );
    }

    /**
     * Returns current layout images settings (sizes)
     */
    public function getCurrentImagesSettings(): array
    {
        return Database::getRepo(\XLite\Model\ImageSettings::class)
            ->findByModuleName($this->getCurrentSkinModuleId());
    }

    /**
     * Returns skin module preview image URL
     */
    protected function getSkinPreviewUrl(
        string $prefix,
        array $module = [],
        string $color = '',
        string $type = ''
    ): string {
        $path = $module
            ? 'modules/' . $module['author'] . '/' . $module['name'] . '/images/'
            : 'images/layout/';

        $image = $prefix . ($color ? ('_' . $color) : '') . ($type ? ('_' . $type) : '') . '.jpg';

        $result = Layout::getInstance()->getSkinPreviewWebPath($path . $image);

        if ($result === null && $color) {
            $image  = $prefix . ($color ? ('_' . $color) : '') . '.jpg';
            $result = Layout::getInstance()->getSkinPreviewWebPath($path . $image);
        }

        if ($result === null && $type) {
            $image  = $prefix . ($type ? ('_' . $type) : '') . '.jpg';
            $result = Layout::getInstance()->getSkinPreviewWebPath($path . $image);
        }

        // Standard skin
        if (!$module) {
            $result = Layout::getInstance()->getResourceWebPath($path . $image);
        }

        return $result ?: Layout::getInstance()->getResourceWebPath('images/layout/' . $prefix . '_placeholder.jpg');
    }
}
