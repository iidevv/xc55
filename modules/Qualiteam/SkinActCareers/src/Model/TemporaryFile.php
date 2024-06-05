<?php


namespace Qualiteam\SkinActCareers\Model;

use XCart\Extender\Mapping\Extender;


/**
 * @Extender\Mixin
 */
class TemporaryFile extends \XLite\Model\TemporaryFile
{
    public function renewMimes()
    {
        $mimes = [
            'image/jpeg' => 'jpeg',
            'image/jpg' => 'jpg',
            'image/png' => 'png',
            'application/msword' => 'doc',
            'application/pdf' => 'pdf',
        ];

        static::$types = $mimes;
    }
}