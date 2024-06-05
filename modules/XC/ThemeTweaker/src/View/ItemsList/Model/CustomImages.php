<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\View\ItemsList\Model;

/**
 * Custom images
 */
class CustomImages extends \XLite\View\ItemsList\Model\Table
{
    protected $images;

    /**
     * @return array
     */
    protected function defineColumns()
    {
        $columns = [];

        $columns['image'] = [
            static::COLUMN_NAME     => static::t('Image'),
            static::COLUMN_CLASS    => 'XC\ThemeTweaker\View\FormField\Inline\FileUploader\Image',
            static::COLUMN_ORDERBY  => 100,
        ];

        $columns['name'] = [
            static::COLUMN_NAME     => static::t('Path for use in custom CSS'),
            static::COLUMN_CLASS    => 'XLite\View\FormField\Inline\Label',
            static::COLUMN_ORDERBY  => 150,
        ];

        $columns['frontURL'] = [
            static::COLUMN_NAME     => static::t('Url'),
            static::COLUMN_CLASS    => 'XLite\View\FormField\Inline\Label',
            static::COLUMN_MAIN     => true,
            static::COLUMN_ORDERBY  => 200,
        ];

        return $columns;
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/XC/ThemeTweaker/images_settings/style.css';

        return $list;
    }

    /**
     * @return integer
     */
    protected function isInlineCreation()
    {
        return static::CREATE_INLINE_TOP;
    }

    /**
     * @return string
     */
    protected function defineRepositoryName()
    {
        return null;
    }

    protected function isRemoved()
    {
        return true;
    }

    protected function removeEntity($entity)
    {
    }

    protected function isListBlank()
    {
        return false;
    }

    protected function isPanelVisible()
    {
        return false;
    }

    public function process()
    {
    }

    /**
     * @return \XLite\Model\AEntity
     */
    protected function getDumpEntity()
    {
        return new \XC\ThemeTweaker\Model\ImagesDataCell();
    }

    /**
     * @param \XLite\Core\CommonCell $cnd
     * @param boolean                $countOnly
     *
     * @return array|integer
     */
    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        $images = $this->getImages();
        if ($countOnly) {
            return count($this->getImages());
        }

        [$offset, $length] = $cnd->getData()['limit'];
        $images = array_slice($images, $offset, $length);

        $data = [];
        foreach ($images as $id => $image) {
            $data[] = new \XC\ThemeTweaker\Model\ImagesDataCell([
                'id' => $id + 1,
                'name' => LC_DS . substr($this->getImagesDir(), strlen(LC_DIR_PUBLIC)) . $image->getFileName(),
                'path' => $image->getPathName(),
                'imageUrl' => $this->getImageUrl($image->getFileName())
            ]);
        }

        return $data;
    }

    /**
     * Get image dir
     *
     * @param string $image Image
     *
     * @return string
     */
    protected function getImageUrl($image)
    {
        return \XC\ThemeTweaker\Main::getResourceURL($this->getImagesDir() . $image);
    }

    /**
     * Get iterator for template files
     *
     * @return \Includes\Utils\FileFilter
     */
    protected function getImagesIterator()
    {
        return new \Includes\Utils\FileFilter(
            $this->getImagesDir()
        );
    }

    /**
     * Get images
     *
     * @return array
     */
    protected function getImages()
    {
        if (!isset($this->images)) {
            $this->images = [];
            try {
                foreach ($this->getImagesIterator()->getIterator() as $file) {
                    if ($file->isFile()) {
                        $this->images[] = $file;
                    }
                }
            } catch (\Exception $e) {
            }
        }

        return $this->images;
    }

    /**
     * Get images dir
     *
     * @return string
     */
    protected function getImagesDir()
    {
        return \XC\ThemeTweaker\Main::getThemeDir() . 'images' . LC_DS;
    }
}
