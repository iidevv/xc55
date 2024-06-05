<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoFeature\Controller\Admin;

use Qualiteam\SkinActVideoFeature\Model\DTO\EducationalVideo\Info;
use Qualiteam\SkinActVideoFeature\Model\VideoCategory as VideoCategoryModel;
use Qualiteam\SkinActVideoFeature\Model\EducationalVideo as EducationalVideoModel;
use Qualiteam\SkinActVideoFeature\View\FormModel\EducationalVideo\Info as InfoFormModel;
use XLite\Core\Database;
use XLite\Core\TopMessage;

class EducationalVideo extends \XLite\Controller\Admin\ACL\Catalog
{
    use \XLite\Controller\Features\FormModelControllerTrait;

    /**
     * Backward compatibility
     *
     * @var array
     */
    protected $params = ['target', 'id', 'page', 'backURL'];

    /**
     * Chuck length
     */
    public const CHUNK_LENGTH = 100;

    // {{{ Abstract method implementations

    /**
     * Check if we need to create new video or modify an existing one
     *
     * NOTE: this function is public since it's neede for widgets
     *
     * @return bool
     */
    public function isNew()
    {
        return !$this->getVideo()->isPersistent();
    }

    /**
     * Alias
     *
     * @return EducationalVideoModel
     */
    protected function getEntity()
    {
        return $this->getVideo();
    }

    protected function addBaseLocation()
    {
        parent::addBaseLocation();

        $this->addLocationNode(
            static::t('SkinActVideoFeature educational videos'),
            $this->buildURL('educational_videos')
        );
    }

    // }}}

    // {{{ Pages

    /**
     * Get pages sections
     *
     * @return array
     */
    public function getPages()
    {
        $list = parent::getPages();
        $list['info'] = static::t('SkinActVideoFeature info');

        return $list;
    }

    /**
     * Get pages templates
     *
     * @return array
     */
    protected function getPageTemplates()
    {
        $list = parent::getPageTemplates();
        $list['info']    = 'modules/Qualiteam/SkinActVideoFeature/educational_video/info.twig';
        $list['default'] = 'modules/Qualiteam/SkinActVideoFeature/educational_video/info.twig';

        return $list;
    }

    // }}}

    // {{{ Data management

    /**
     * Alias
     *
     * @return EducationalVideoModel
     */
    public function getVideo()
    {
        $result = $this->videoCache
            ?: Database::getRepo(EducationalVideoModel::class)->find($this->getVideoId());

        if ($result === null) {
            $result = new EducationalVideoModel;

            if (
                \XLite\Core\Request::getInstance()->category_id > 1 &&
                ($category = Database::getRepo(VideoCategoryModel::class)->find(\XLite\Core\Request::getInstance()->category_id))
            ) {
                $result->addCategory($category);
            }
        }

        return $result;
    }

    /**
     * Returns the categories of the video
     *
     * @return array
     */
    public function getCategories()
    {
        return $this->isNew()
            ? [
                $this->getCategoryId(),
            ]
            : $this->getVideo()->getCategories();
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getVideo() && $this->getVideo()->isPersistent()
            ? $this->getVideo()->getDescription()
            : static::t('SkinActVideoFeature new video');
    }

    /**
     * Get video category id
     *
     * @return integer
     */
    public function getCategoryId()
    {
        $categoryId = parent::getCategoryId();

        if (empty($categoryId) && !$this->isNew()) {
            $categoryId = $this->getVideo()->getCategoryId();
        }

        return $categoryId;
    }

    /**
     * Return current video Id
     *
     * NOTE: this function is public since it's neede for widgets
     *
     * @return integer
     */
    public function getVideoId()
    {
        $result = $this->videoCache
            ? $this->videoCache->getVideoId()
            : (int) \XLite\Core\Request::getInstance()->id;

        if (0 >= $result) {
            $result = (int) \XLite\Core\Request::getInstance()->id;
        }

        return $result;
    }

    /**
     * The video can be set from the view classes
     *
     * @param EducationalVideoModel $video Video
     */
    public function setVideo(EducationalVideoModel $video)
    {
        $this->videoCache = $video;
    }

    /**
     * Get posted data
     *
     * @param string $field Name of the field to retrieve OPTIONAL
     *
     * @return mixed
     */
    protected function getPostedData($field = null)
    {
        $value = parent::getPostedData($field);

        $time = \XLite\Core\Converter::time();

        if ($field === null) {
            if (isset($value['date'])) {
                $value['date'] = ((int) strtotime($value['date']))
                    ?: mktime(0, 0, 0, date('m', $time), date('j', $time), date('Y', $time));
            }
        } elseif ($field === 'date') {
            $value = ((int) strtotime($value)) ?: mktime(0, 0, 0, date('m', $time), date('j', $time), date('Y', $time));
        }

        return $value;
    }

    // }}}

    // {{{ Action handlers

    protected function doActionUpdate()
    {
        $dto = $this->getFormModelObject();
        $video = $this->getVideo();
        $isPersistent = $video->isPersistent();

        $formModel = new InfoFormModel(['object' => $dto]);

        $form = $formModel->getForm();
        $data = \XLite\Core\Request::getInstance()->getData();
        $rawData = \XLite\Core\Request::getInstance()->getNonFilteredData();

        $form->submit($data[$this->formName]);

        if ($form->isValid()) {
            $dto->populateTo($video, $rawData[$this->formName]);

            if ($video->getVideoId()) {
                Database::getEM()->getUnitOfWork()->scheduleForUpdate($video);
            }
            Database::getEM()->persist($video);
            Database::getEM()->flush();

            $dto->afterPopulate($video, $rawData[$this->formName]);
            if (!$isPersistent) {
                $dto->afterCreate($video, $rawData[$this->formName]);
                TopMessage::addInfo('SkinActVideoFeature video has been created');
            } else {
                $dto->afterUpdate($video, $rawData[$this->formName]);
                TopMessage::addInfo('SkinActVideoFeature video has been updated');
            }
            Database::getEM()->flush();
        } else {
            $this->saveFormModelTmpData($rawData[$this->formName]);

            foreach ($form->getErrors(true) as $error) {
                TopMessage::addError($error->getMessage());
            }
        }

        $videoId = $video->getVideoId() ?: $this->getVideoId();

        $params = $videoId ? ['id' => $videoId] : [];

        $this->setReturnURL($this->buildURL('educational_video', '', $params));
    }

    /**
     * @return \XLite\Model\DTO\Base\ADTO
     */
    public function getFormModelObject()
    {
    return new Info($this->getVideo());
    }

    /**
     * Purify an attribute value
     *
     * @param array $value
     *
     * @return array
     */
    protected function purifyValue($value)
    {
        $value['value'] = \XLite\Core\HTMLPurifier::purify($value['value']);

        return $value;
    }

    // }}}
}