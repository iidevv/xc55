<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Utils\Module;

use Includes\Utils\FileManager;
use Includes\Utils\PropertyBag;

/**
 * Internal module metadata representation
 *
 * @property string  $id
 * @property string  $version          Module version
 * @property string  $type             Module type
 * @property string  $author           Module author key
 * @property string  $name             Module key
 * @property string  $authorName       Module display author
 * @property string  $moduleName       Module display name
 * @property string  $description      Module description
 * @property string  $minorRequiredCoreVersion
 * @property array   $dependsOn        Ids of other modules, required for this module to enable
 * @property array   $incompatibleWith Ids of other modules, incompatible with this module
 * @property bool    $showSettingsForm
 * @property bool    $isSystem
 * @property bool    $canDisable
 * @property array   $autoloader       Path to autoload
 * @property boolean $enabled          Module state
 * @property boolean $activeSkin       If this skin module is an active skin
 * @property boolean $yamlLoaded       Are module yaml files loaded?
 * @property array   $layoutColors     Layout colors of skin module
 * @property array   $directories      Module directories cache
 * @property array   $service
 */
class Module extends PropertyBag
{
    public const ID_SEPARATOR = '-';

    public function __construct(array $data = null)
    {
        if (!isset($data['enabled'])) {
            $data['enabled'] = false;
        }

        if (!isset($data['yamlLoaded'])) {
            $data['yamlLoaded'] = false;
        }

        parent::__construct($data);
    }

    /**
     * @param string $author
     * @param string $name
     *
     * @return string
     */
    public static function buildId($author, $name)
    {
        [$author, $name] = static::explodeModuleId($author, $name);

        return $author . self::ID_SEPARATOR . $name;
    }

    /**
     * @param string $author
     * @param string $name
     *
     * @return array
     */
    public static function explodeModuleId($author, $name = null)
    {
        if ($name === null) {
            $result = preg_split('/\\\\|-/', $author);
            if (count($result) === 2) {
                return $result;
            }
        }

        return [$author, $name];
    }

    /**
     * @param array|string $xcartId
     *
     * @return array|string
     */
    public static function convertId($xcartId)
    {
        if (is_array($xcartId)) {
            return array_map(static function ($item) {
                return static::convertId($item);
            }, $xcartId);
        }

        return str_replace('\\', self::ID_SEPARATOR, $xcartId);
    }

    /**
     * @param string $path
     *
     * @return null|string
     */
    public static function getModuleIdByFilePath($path)
    {
        return preg_match(static::getModuleIdByFilePathPattern(), $path, $matches)
            ? static::buildId($matches[1], $matches[2])
            : null;
    }

    /**
     * @param string $className
     *
     * @return null|string
     */
    public static function getModuleIdByClassName($className)
    {
        return preg_match(static::getModuleIdByClassNamePattern(), $className, $matches) && $matches[1] !== 'XLite'
            ? static::buildId($matches[1], $matches[2])
            : null;
    }

    /**
     * @param string $moduleId
     * @param string $methodName
     * @param array  $arguments
     *
     * @return mixed
     */
    public static function callMainClassMethod($moduleId, $methodName, array $arguments = [])
    {
        $className = static::getMainClassName($moduleId);

        return class_exists($className) && method_exists($className, $methodName)
            ? call_user_func_array([$className, $methodName], $arguments)
            : static::tryLoadingOriginalMainAndCallMethod($moduleId, $methodName, $arguments);
    }

    /**
     * @param string      $author
     * @param string|null $name
     *
     * @return string
     */
    public static function getMainClassName($author, $name = null)
    {
        [$author, $name] = static::explodeModuleId($author, $name);

        return $author . '\\' . $name . '\Main';
    }

    /**
     * @param string      $author
     * @param string|null $name
     *
     * @return string
     */
    public static function getMainClassFilePath($author, $name = null)
    {
        $sourcePath = static::getSourcePath($author, $name);

        return $sourcePath . 'src/Main.php';
    }

    /**
     * @param string      $author
     * @param string|null $name
     *
     * @return string
     */
    public static function getMainDataFilePath($author, $name = null)
    {
        $sourcePath = static::getSourcePath($author, $name);

        return $sourcePath . 'config/main.yaml';
    }

    /**
     * @param string      $author
     * @param string|null $name
     *
     * @return string
     */
    public static function getSourcePath($author, $name = null)
    {
        [$author, $name] = static::explodeModuleId($author, $name);

        return \LC_DIR_MODULES . $author . \LC_DS . $name . \LC_DS;
    }

    /**
     * @param string      $author
     * @param string|null $name
     *
     * @return string|null
     */
    public static function getIconURL($author, $name = null)
    {
        [$author, $name] = static::explodeModuleId($author, $name);

        return ($author === 'CDev' && $name === 'Core')
            ? 'assets/web/admin/images/core_image.png'
            : "modules/{$author}/{$name}/images/icon.png";
    }

    /**
     * @param string      $author
     * @param string|null $name
     *
     * @return string
     */
    public static function getSkinPreviewURL($author, $name = null)
    {
        [$author, $name] = static::explodeModuleId($author, $name);

        $icon = 'assets/web/admin' . \LC_DS . 'modules' . \LC_DS . $author . \LC_DS . $name . \LC_DS . 'preview_list.jpg';

        if (!FileManager::isFileReadable(\LC_DIR_SKINS . \LC_DS . $icon)) {
            return '';
        }

        return 'assets' . \LC_DS . $icon;
    }

    /**
     * @param string $moduleId
     * @param string $methodName
     * @param array  $arguments
     *
     * @return mixed
     */
    protected static function tryLoadingOriginalMainAndCallMethod($moduleId, $methodName, array $arguments = [])
    {
        $classPath = static::getMainClassFilePath($moduleId);
        $className = static::getMainClassName($moduleId);

        if (file_exists($classPath) && !class_exists($className, false)) {
            require_once $classPath;
        }

        return method_exists($className, $methodName)
            ? call_user_func_array([$className, $methodName], $arguments)
            : null;
    }

    /**
     * @return string
     */
    protected static function getModuleIdByFilePathPattern()
    {
        return implode(
            preg_quote(\LC_DS, '/'),
            ['/classes', 'XLite', 'Module', '(\w+)', '(\w+)', '/S']
        );
    }

    /**
     * @return string
     */
    protected static function getModuleIdByClassNamePattern()
    {
        return '/(?:\\\\)?(\w+)\\\\(\w+)(\\\\|$)/S';
    }

    /**
     * @return string
     */
    public function getDisplayAuthor()
    {
        return $this->authorName;
    }

    /**
     * @return string
     */
    public function getDisplayName()
    {
        return $this->moduleName;
    }

    /**
     * @return array
     */
    public function getLayoutColors()
    {
        return $this->callClassMethod('getLayoutColors');
    }

    /**
     * @param string $methodName
     * @param array  $arguments
     *
     * @return mixed
     */
    public function callClassMethod($methodName, array $arguments = [])
    {
        return static::callMainClassMethod($this->id, $methodName, $arguments);
    }

    /**
     * @param bool $skipMainClassCheck
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @return bool
     */
    public function isActiveSkin()
    {
        return $this->isEnabled()
            && $this->isSkin()
            && $this->activeSkin;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @return bool
     */
    public function isSkin()
    {
        return $this->type === 'skin';
    }

    /**
     * @return bool
     */
    public function isPayment()
    {
        return $this->type === 'payment';
    }

    /**
     * @return bool
     */
    public function isShipping()
    {
        return $this->type === 'shipping';
    }

    public function hasSettingsForm()
    {
        return $this->callClassMethod('showSettingsForm');
    }

    /**
     * Return list of module directories which contain class files
     *
     * @return array
     */
    protected function getClassDirs()
    {
        return [
            static::getSourcePath($this->author, $this->name),
        ];
    }
}
