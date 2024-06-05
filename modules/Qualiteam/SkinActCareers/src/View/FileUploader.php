<?php


namespace Qualiteam\SkinActCareers\View;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Request;


/**
 * @Extender\Mixin
 */
class FileUploader extends \XLite\View\FileUploader
{

    public function getCommonFiles()
    {
        $list = parent::getCommonFiles();

//        $target = \XLite::getController()->getTarget();
//
//        if ($target === 'interview_questions') {
//            foreach ($list['js'] as $ind => $val) {
//                if ($val === 'file_uploader/controller.js') {
//                    $list['js'][$ind] = 'modules/Qualiteam/SkinActCareers/controller.js';
//                }
//            }
//        }

        return $list;
    }

    protected function getPreview()
    {
        $preview = parent::getPreview();

        if (Request::getInstance()->interview > 0 && $preview === '') {
            return $this->getObject() ? $this->getObject()->getFileName() : '';
        }

        return $preview;
    }
}