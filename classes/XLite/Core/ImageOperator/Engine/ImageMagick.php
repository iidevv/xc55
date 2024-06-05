<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\ImageOperator\Engine;

use XLite\Core\ImageOperator\ADTO;
use XLite\Core\ImageOperator\DTO\Local;
use XLite\InjectLoggerTrait;
use XLite\Model\Base\Image;

/**
 * ImageMagic engine
 */
class ImageMagick extends \XLite\Core\ImageOperator\AEngine
{
    use InjectLoggerTrait;

    /**
     * @var string
     */
    protected static $imageMagickExecutable;

    /**
     * @var string
     */
    protected $resource;

    protected static ?array $formatsListCache = null;

    /**
     * Check - enabled engine or not
     *
     * @return boolean
     */
    public static function isEnabled()
    {
        return parent::isEnabled()
            && (bool) static::getImageMagickExecutable();
    }

    /**
     * Return Image Magick executable
     *
     * @return string
     */
    public static function getImageMagickExecutable()
    {
        if (self::$imageMagickExecutable === null) {
            $imageMagickPath = \Includes\Utils\ConfigParser::getOptions(['images', 'image_magick_path']);

            if ($imageMagickPath) {
                self::$imageMagickExecutable = \Includes\Utils\FileManager::findExecutable($imageMagickPath . 'convert')
                    ?: \Includes\Utils\FileManager::findExecutable($imageMagickPath . 'magick'); // IM v7+
            }
        }

        return self::$imageMagickExecutable;
    }

    public function __construct()
    {
        parent::__construct();

        $this->options['image_magick_path']
            = \Includes\Utils\ConfigParser::getOptions(['images', 'image_magick_path']);
    }

    protected function processWebpWhileDoNotSupportWebp(): bool
    {
        $image = $this->getImage();

        $result = $image->getType() === Image::WEBP_MIME_TYPE && !$this->isWebpSupported();

        if ($result) {
            $this->getLogger()->warning(
                'Installed ImageMagick version does not support WebP format. Thumbnails have not been generated, original image will be shown instead.',
                [
                    'Image' => $image->getName()
                ]
            );
        }

        return $result;
    }

    /**
     * Resize procedure
     *
     * @param integer $width  Width
     * @param integer $height Height
     *
     * @return boolean
     */
    public function resize($width, $height)
    {
        $result = false;

        if ($this->processWebpWhileDoNotSupportWebp()) {
            return false;
        }

        $newResource = tempnam(LC_DIR_TMP, 'image.new');
        if (
            $this->resource
            && $this->execFilmStripLook($newResource, $this->getImage()->getType()) === 0
            && $this->execResize($newResource, $width, $height) === 0
        ) {
            copy($newResource, $this->resource);
            $this->updateImageFromResource();

            $result = true;
        }

        unlink($newResource);

        return $result;
    }

    /**
     * Resize bulk
     *
     * @param array $sizes
     *
     * @return array
     */
    public function resizeBulk($sizes)
    {
        if (empty($sizes)) {
            return [];
        }

        $result = [];

        $newResource = tempnam(LC_DIR_TMP, 'image.new');
        $last        = end($sizes);
        $cmd         = '"' . static::getImageMagickExecutable()
            . '" -quality ' . $this->options['resize_quality'] . ' ';

        if (\Includes\Utils\ConfigParser::getOptions(['images', 'make_progressive'])) {
            $cmd .= '-interlace Plane ';
        }

        $cmd .= $newResource . ' \\' . PHP_EOL;

        if ($this->processWebpWhileDoNotSupportWebp()) {
            return [];
        }

        if (
            $this->resource
            && $this->execFilmStripLook($newResource, $this->getImage()->getType()) === 0
        ) {
            foreach ($sizes as $key => $size) {
                $lastRow = $last === $size ? true : false;

                $resizedTmp         = tempnam(LC_DIR_TMP, 'image.new');
                $sizes[$key]['tmp'] = $resizedTmp;

                if (!$lastRow) {
                    $cmd .= '\( +clone ';
                }

                $cmd .= '-resize ' . $size['width'] . 'x' . $size['height'] . ' -strip ';

                if (!$lastRow) {
                    $cmd .= '-write ';
                }

                $cmd .= $resizedTmp;

                if (!$lastRow) {
                    $cmd .= ' +delete \) \\' . PHP_EOL;
                }
            }

            exec($cmd, $output, $result);

            foreach ($sizes as $key => $size) {
                $sizes[$key]['tmp'] = new Local($size['tmp']);
                unlink($size['tmp']);
            }

            $result = $sizes;
        }

        unlink($newResource);

        return $result;
    }

    public function rotate($degree)
    {
        $result = false;

        if ($this->processWebpWhileDoNotSupportWebp()) {
            return false;
        }

        $newResource = tempnam(LC_DIR_TMP, 'image.new');
        if (
            $this->resource
            && $this->execFilmStripLook($newResource, $this->getImage()->getType()) === 0
            && $this->execRotate($newResource, $degree) === 0
        ) {
            copy($newResource, $this->resource);
            $this->updateImageFromResource();

            $result = true;
        }

        unlink($newResource);

        return $result;
    }

    public function mirror($horizontal = true)
    {
        $result = false;

        if ($this->processWebpWhileDoNotSupportWebp()) {
            return false;
        }

        $newResource = tempnam(LC_DIR_TMP, 'image.new');
        if (
            $this->resource
            && $this->execFilmStripLook($newResource, $this->getImage()->getType()) === 0
            && $this->execMirror($newResource, $horizontal) === 0
        ) {
            copy($newResource, $this->resource);
            $this->updateImageFromResource();

            $result = true;
        }

        unlink($newResource);

        return $result;
    }

    /**
     * Execution of preparing film strip look
     *
     * @param string $newResource File path to new image
     * @param string fileType of original file
     *
     * @return integer
     */
    protected function execFilmStripLook($newResource, $fileType)
    {
        $options = ' ';

        if ($fileType === 'image/gif') {
            $options = ' -coalesce ';
        }

        exec(
            '"' . static::getImageMagickExecutable()
            . '" ' . $this->resource . $options
            . $newResource,
            $output,
            $result
        );
        return $result;
    }

    /**
     * Execution of resizing
     *
     * @param string  $newImage File path to new image
     * @param integer $width    Width
     * @param integer $height   Height
     *
     * @return integer
     */
    protected function execResize($newImage, $width, $height)
    {
        $cmd = '"' . static::getImageMagickExecutable() . '" '
            . $newImage
            . ' -resize '
            . $width . 'x' . $height
            . ' -quality ' . $this->options['resize_quality'] . ' ';

        if (\Includes\Utils\ConfigParser::getOptions(['images', 'make_progressive'])) {
            $cmd .= '-strip -interlace Plane ';
        }

        $cmd .= $newImage;

        exec($cmd, $output, $result);

        return $result;
    }

    /**
     * Execution of rotating
     *
     * @param string  $newImage File path to new image
     * @param float $degree
     *
     * @return integer
     */
    protected function execRotate($newImage, $degree)
    {
        $quality = 100;

        exec(
            '"' . static::getImageMagickExecutable() . '" '
            . $newImage
            . ' -rotate '
            . "-\"{$degree}\""
            . " -quality {$quality} "
            . $newImage,
            $output,
            $result
        );

        return $result;
    }

    /**
     * Execution of flipfloping
     *
     * @param string  $newImage File path to new image
     * @param boolean $horizontal
     *
     * @return integer
     */
    protected function execMirror($newImage, $horizontal)
    {
        exec(
            '"' . static::getImageMagickExecutable() . '" '
            . $newImage
            . ($horizontal ? ' -flop ' : ' -flip ')
            . $newImage,
            $output,
            $result
        );

        return $result;
    }

    protected function updateImageFromResource()
    {
        $image = $this->getImage();
        $resource = $this->resource;

        if ($resource) {
            $image->setBody(file_get_contents($resource));
            $imageSize = @getimagesize($resource);

            if ($imageSize) {
                $image->setWidth($imageSize[0]);
                $image->setHeight($imageSize[1]);
            }
        }
    }

    /**
     * Set image
     *
     * @param ADTO $image Image
     */
    public function setImage($image)
    {
        parent::setImage($image);

        if ($this->resource) {
            unlink($this->resource);
            $this->resource = null;
        }

        $body = $image->getBody();
        if ($body) {
            $this->resource = tempnam(LC_DIR_TMP, 'image');
            file_put_contents($this->resource, $body);
        }
    }

    public function __destruct()
    {
        if ($this->resource) {
            unlink($this->resource);
        }
    }

    public function isWebpSupported(): bool
    {
        if (!static::isEnabled()) {
            return false;
        }

        $format = $this->queryFormats()[strtoupper(Image::WEBP_EXTENSION)] ?? [];

        return (!empty($format['read']) && !empty($format['write']));
    }

    public function queryFormats(): array
    {
        if (static::$formatsListCache === null) {
            $lines = explode(
                "\n",
                shell_exec(
                    '"' . static::getImageMagickExecutable() . '" identify -list format'
                )
            );

            array_shift($lines);
            array_shift($lines);

            $lines = array_map(
                static function (string $line): array {
                    return preg_split('/\s+/', trim($line));
                },
                $lines
            );

            static::$formatsListCache = [];

            foreach ($lines as $line) {
                if (
                    count($line) > 3
                    && strlen($line[2]) === 3
                    && in_array($line[2][0], ['r', '-'], true)
                    && in_array($line[2][1], ['w', '-'], true)
                    && in_array($line[2][2], ['+', '-'], true)
                ) {
                    static::$formatsListCache[$line[1]] = [
                        'read'     => ($line[2][0] === 'r'),
                        'write'    => ($line[2][1] === 'w'),
                        'multiple' => ($line[2][2] === '+')
                    ];
                }
            }
        }

        return static::$formatsListCache;
    }

    public function getName(): string
    {
        return 'ImageMagick';
    }
}
