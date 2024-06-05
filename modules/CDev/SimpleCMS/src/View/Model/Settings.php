<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\SimpleCMS\View\Model;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class Settings extends \XLite\View\Model\Settings
{
    /**
     * Logo & Favicon fields
     *
     * @var array
     */
    protected static $logoFaviconFields = ['logo', 'mobileLogo', 'favicon', 'appleIcon'];

    /**
     *
     */
    protected static $showDefaultMenu = 'show_default_menu';

    /**
     * Logo & Favicon validation flag
     *
     * @var boolean
     */
    protected $logoFaviconValidation = true;

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/CDev/SimpleCMS/settings.less';

        return $list;
    }

    /**
     * Defines the subdirectory where images (logo, favicon) will be stored
     *
     * @return string
     */
    protected static function getLogoFaviconSubDir()
    {
        return \Includes\Utils\FileManager::getRelativePath(LC_DIR_IMAGES, LC_DIR_ROOT) . LC_DS . 'simplecms' . LC_DS;
    }

    /**
     * Defines the server directory where images (logo, favicon) will be stored
     *
     * @return string
     */
    protected static function getLogoFaviconDir()
    {
        return LC_DIR_ROOT . static::getLogoFaviconSubDir();
    }

    /**
     * Check for the form errors
     *
     * @return boolean
     */
    public function isValid()
    {
        return parent::isValid() && $this->logoFaviconValidation;
    }

    /**
     * Populate model object properties by the passed data
     *
     * @param array $data Data to set
     *
     * @return void
     */
    protected function setModelProperties(array $data)
    {
        if ($this->getTarget() === 'logo_favicon') {
            if (isset($data['logo']['alt'])) {
                \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption(
                    [
                        'category' => 'CDev\SimpleCMS',
                        'name'     => 'logo_alt',
                        'value'    => $data['logo']['alt'],
                    ]
                );
            }

            foreach ($this->formFields as $section) {
                foreach ($section[static::SECTION_PARAM_FIELDS] as $k => $v) {
                    if (in_array($v->getName(), static::$logoFaviconFields, true)) {
                        $data[$v->getName()] = ($v->getValue())
                            ? $this->prepareImageData($v->getValue(), $v->getName())
                            : \XLite\Core\Config::getInstance()->CDev->SimpleCMS->{$v->getName()};
                    }
                }
            }
        }

        parent::setModelProperties($data);

        if (
            $this->getTarget() === 'logo_favicon'
            && (
                !empty($data['logo'])
                || !empty(\XLite\Core\Request::getInstance()->useDefaultImage['logo'])
            )
        ) {
            \XLite\Core\Config::updateInstance();
            $logoImage = \XLite\Core\Database::getRepo('XLite\Model\Image\Common\Logo')->getLogo();
            $logoImage->prepareSizes(true);
        }
    }

    /**
     * Get schema fields
     *
     * @return array
     */
    public function getSchemaFieldsForSection($section)
    {
        $list = parent::getSchemaFieldsForSection($section);

        if (
            $this->getTarget() === 'logo_favicon'
            || ($this->getTarget() === 'module'
                && $this->getModule()
                && $this->getModule() === 'CDev-SimpleCMS'
            )
        ) {
            foreach ($list as $k => $v) {
                $id = is_object($v) && property_exists($v, 'name') ? $v->name : $k;
                if (
                    ($this->getTarget() === 'logo_favicon'
                        && !in_array($id, static::$logoFaviconFields, true)
                    )
                    || ($this->getTarget() !== 'logo_favicon'
                        && in_array($id, static::$logoFaviconFields, true)
                    )
                ) {
                    unset($list[$k]);
                }
            }
        }

        return $list;
    }

    /**
     * Additional preparations for images.
     * Upload them into specific directory
     *
     * @param array  $optionValue Option value
     * @param string $imageType   Image type
     *
     * @return string
     */
    protected function prepareImageData(array $optionValue, string $imageType): string
    {
        $currentFile = \XLite\Core\Config::getInstance()->CDev->SimpleCMS->{$imageType};

        $dir = static::getLogoFaviconDir();

        $fileMustBeDeleted = isset($optionValue['delete']);
        if ($fileMustBeDeleted) {
            \Includes\Utils\FileManager::deleteFile(\XLite\Core\Config::getInstance()->CDev->SimpleCMS->{$imageType});
            return '';
        }

        $temporaryFile = isset($optionValue['temp_id'])
            ? \XLite\Core\Database::getRepo('\XLite\Model\TemporaryFile')->find($optionValue['temp_id'])
            : null;

        if (!$temporaryFile) {
            return $currentFile;
        }

        $originalName = $temporaryFile->getPath();
        $realName = preg_replace('/([^a-zA-Z0-9_\-\.]+)/', '_', $originalName);
        $realName = $imageType . '_' . $realName;

        $validImage = $imageType === 'appleIcon'
                ? $this->isValidAppleIcon($temporaryFile->getStoragePath(), $realName)
                : true;

        if (!$validImage) {
            $this->logoFaviconValidation = false;
            if ($imageType === 'appleIcon') {
                \XLite\Core\TopMessage::addError(
                    'The AppleIcon image could not be uploaded (Unallowed image type. Must be a .png image with the resolution of 192x192 px)',
                    [
                        'file' => $originalName,
                    ]
                );
            } else {
                \XLite\Core\TopMessage::addError(
                    'The "{{file}}" file is not allowed image and was not uploaded. Allowed images are: {{extensions}}',
                    [
                        'file' => $originalName,
                        'extensions' => implode(', ', $this->getImageExtensions()),
                    ]
                );
            }
            return $currentFile;
        }

        if (!\Includes\Utils\FileManager::isDir($dir)) {
            \Includes\Utils\FileManager::mkdirRecursive($dir);
        }

        if (\Includes\Utils\FileManager::isDir($dir)) {
            // Remove current file if it is not default
            if ($currentFile) {
                \Includes\Utils\FileManager::deleteFile($currentFile);
            }

            $pathFrom = $temporaryFile->getStoragePath();
            $pathTo = $dir . ($imageType === 'favicon' ? static::FAVICON : $realName);

            $fileIsMoved = \Includes\Utils\FileManager::move($pathFrom, $pathTo, true);
            if ($fileIsMoved) {
                \Includes\Utils\FileManager::chmod($pathTo, 0644);
            }

            \XLite\Core\Database::getEM()->remove($temporaryFile);
            \XLite\Core\Database::getEM()->flush();

            $optionValue = static::getLogoFaviconSubDir() . ($imageType === 'favicon' ? static::FAVICON : $realName);

            $publicDir = 'public/';
            if (substr($optionValue, 0, strlen($publicDir)) === $publicDir) {
                $optionValue = substr($optionValue, strlen($publicDir));
            }
        }

        return $optionValue;
    }

    /**
     * Check if file is valid image
     *
     * @param string $path Temporary uploaded file path
     * @param string $name Real file name
     *
     * @return boolean
     */
    protected function isValidAppleIcon($path, $name)
    {
        return strtolower(pathinfo($name, PATHINFO_EXTENSION)) === 'png'
            && $this->isValidResolution($path, '192x192');
    }

    /**
     * @param $path
     * @param $resolution
     *
     * @return bool
     */
    protected function isValidResolution($path, $resolution)
    {
        $data = @getimagesize($path);

        return is_array($data)
            ? $data[0] . 'x' . $data[1] === $resolution
            : true;
    }
    /**
     * Return true if file has non-empty name
     *
     * @param string $path File path
     *
     * @return boolean
     */
    protected function hasImageName($path)
    {
        return 0 < strlen(trim(pathinfo($path, PATHINFO_FILENAME)));
    }

    /**
     * Get list of allowed image extensions
     *
     * @return array
     */
    protected function getImageExtensions()
    {
        return ['gif', 'jpg', 'jpeg', 'png', 'ico'];
    }
}
