<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoFeature\Model\DTO\EducationalVideo;

use Qualiteam\SkinActVideoFeature\Model\VideoCategory as VideoCategoryModel;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use XLite\Core\Translation;
use Qualiteam\SkinActVideoFeature\Model\CategoryVideos;
use XLite\Model\DTO\Base\CommonCell;

class Info extends \XLite\Model\DTO\Base\ADTO
{
    /**
     * @param \Qualiteam\SkinActVideoFeature\Model\DTO\EducationalVideo\Info $dto
     * @param ExecutionContextInterface     $context
     */
    public static function validate($dto, ExecutionContextInterface $context)
    {
    }

    /**
     * @param mixed|\Qualiteam\SkinActVideoFeature\Model\EducationalVideo $object
     */
    protected function init($object)
    {
        $categories = [];
        foreach ($object->getCategories() as $category) {
            $categories[] = $category->getCategoryId();
        }

        $default       = [
            'identity' => $object->getVideoId(),

            'description'          => $object->getDescription(),
            'video_code'           => $object->getVideoCode(),
            'youtube_video_id'     => $object->getYoutubeVideoId(),
            'category'             => $categories,
            'category_tree'        => $categories,
            'category_widget_type' => \XLite\Core\Request::getInstance()->video_modify_categroy_widget ?: 'search',
        ];
        $this->default = new CommonCell($default);
    }

    /**
     * @param \Qualiteam\SkinActVideoFeature\Model\EducationalVideo $object
     * @param array|null           $rawData
     *
     * @return mixed
     */
    public function populateTo($object, $rawData = null)
    {
        $default = $this->default;

        $categories = \XLite\Core\Database::getRepo(VideoCategoryModel::class)
            ->findByIds($default->category);

        $order = array_flip($default->category);

        $object->replaceCategoryVideosLinksByCategories($categories, $default->category);

        foreach ($object->getCategoryVideos() as $categoryVideoLink) {
            /** @var CategoryVideos $categoryVideoLink */

            if (!$categoryVideoLink->getCategory()) {
                continue;
            }

            $categoryId = $categoryVideoLink->getCategory()->getCategoryId();

            if (isset($order[$categoryId])) {
                $categoryVideoLink->setOrderbyInVideo($order[$categoryId] * 10);
            }
        }

        $description = $this->isContentTrustedByPermission('description')
            ? (string) $rawData['default']['description']
            : \XLite\Core\HTMLPurifier::purify((string) $rawData['default']['description']);

        $object->setVideoCode($rawData['default']['video_code']);
        $object->setDescription($description);

        $youtubeVideoId = $this->getYoutubeVideoId($rawData);
        $object->setYoutubeVideoId($youtubeVideoId);

        $object->setEnabled($object->getEnabled());
    }

    protected function getYoutubeVideoId($rawData)
    {
        $code = $rawData['default']['video_code'];
        preg_match( '/src="([^"]*)"/i', $code, $link);

        $link = !empty($link) && !empty($link[1]) ? $link[1] : $code;

        preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user|shorts)\/))([^\?&\"'>]+)/", $link, $id);

        return !empty($id) && !empty($id[1]) ? $id[1] : '';
    }

    /**
     * @param \Qualiteam\SkinActVideoFeature\Model\EducationalVideo $object
     * @param array|null           $rawData
     */
    public function afterUpdate($object, $rawData = null)
    {
    }

    /**
     * @param \Qualiteam\SkinActVideoFeature\Model\EducationalVideo $object
     * @param array|null           $rawData
     */
    public function afterPopulate($object, $rawData = null)
    {
        $object->updateQuickData();
    }

    /**
     * @param \Qualiteam\SkinActVideoFeature\Model\EducationalVideo $object
     * @param array|null           $rawData
     */
    public function afterCreate($object, $rawData = null)
    {
    }
}