<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Banner\Controller\Admin;

use Doctrine\ORM\PersistentCollection;
use QSL\Banner\Model\Content;
use QSL\Banner\Model\ContentTranslation;
use XLite\Core\CommonCell;
use XLite\Core\Converter;
use XLite\Core\Database;
use XLite\Core\Request;
use XLite\Core\Session;
use XLite\Core\TopMessage;

/**
 * Download Item controller
 *
 */
class BannerEdit extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Controllers parameters
     *
     * @var   array
     */
    protected $params = ['target', 'id', 'mode', 'page', 'backURL'];

    /**
     * Banner cache var
     *
     * @var   PersistentCollection|null
     */
    protected $bannerCache = null;

    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    public function checkACL()
    {
        return parent::checkACL()
            || \XLite\Core\Auth::getInstance()->isPermissionAllowed(BannersList::PERMISSION_BANNERS);
    }

    /**
     * Get pages sections
     *
     * @return array
     */
    public function getPages()
    {
        $list = parent::getPages();
        $list['info'] = static::t('Banner settings');

        if ($this->getBanner()->getId()) {
            $list['images']    = static::t('Banner images');
            $list['codes'] = static::t('HTML banners');
        }

        return $list;
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/QSL/Banner';
    }

    /**
     * Get pages templates
     *
     * @return array
     */
    public function getPageTemplates()
    {
        $list = parent::getPageTemplates();

        $list['info']    = $this->getDir() . LC_DS . 'banner_body_info.twig';
        $list['default'] = $this->getDir() . LC_DS . 'banner_body_info.twig';

        if ($this->getBanner()->getId()) {
            $list['images'] = $this->getDir() . LC_DS . 'banner_body_images.twig';
            $list['codes']  = $this->getDir() . LC_DS . 'banner_body_codes.twig';
        }

        return $list;
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return !$this->getBanner()->getId()
            ? static::t('Add banner')
            : $this->getBanner()->getTitle();
    }


    /**
     * @return \XLite\Model\AEntity
     */
    public function getBanner()
    {
        return $this->getModelForm()->getModelObject()
            ?:
            Database::getRepo('QSL\Banner\Model\Banner')
                ->find(Request::getInstance()->id);
    }

    /**
     * Get banner content
     *
     * @return PersistentCollection|void
     */
    public function getBannerContent()
    {
        // Find download item in the DB
        if (is_null($this->bannerContentCache)) {
            $this->bannerContentCache = Database::getRepo('QSL\Banner\Model\Content')
                ->find($this->getBannerContentId());
        }

        // Create new object or restore it from the session
        if (is_null($this->bannerContentCache)) {
            $this->bannerContentCache = new Content(
                $this->getBannerContentDataFromSession()
            );
        }

        return $this->bannerContentCache;
    }


    /**
     * Return current banner id
     *
     * @return integer
     */
    public function getBannerContentId()
    {
        return intval(Request::getInstance()->banner_content_id);
    }

    /**
     * Return last order by
     *
     * @return integer
     */
    public function getLastOrderBy()
    {
        $orderBy = Database::getRepo('QSL\Banner\Model\Banner')
            ->getLastOrderBy();

        return intval($orderBy) + 10;
    }

    /**
     * Return last content position
     *
     * @return integer
     */
    public function getLastContOrderBy()
    {
        $orderBy = Database::getRepo('QSL\Banner\Model\Content')
            ->getLastOrderBy();

        return intval($orderBy) + 10;
    }

    /**
     * Get banners data from session
     *
     * @return array
     */
    protected function getBannerDataFromSession()
    {
        return (isset(Session::getInstance()->bannerSavedData))
            ? Session::getInstance()->bannerSavedData
            : [
                'position' => $this->getLastOrderBy()
            ];
    }

    /**
     * Get banner content data from session
     *
     * @return array
     */
    protected function getBannerContentDataFromSession()
    {
        return (isset(Session::getInstance()->bannerContentSavedData))
            ? Session::getInstance()->bannerContentSavedData
            : [
                'position' => $this->getLastContOrderBy()
            ];
    }


    /**
     * @throws \Doctrine\ORM\ORMException
     */
    protected function doActionAddContent()
    {
        $posted_data = Request::getInstance()->getNonFilteredData();
        $data = Database::getRepo('\QSL\Banner\Model\Content')->insert($posted_data["postedData"]);

        if ($data) {
            $banner = $this->getBanner();

            $banner->addContents($data);//

            $data->setBanner($banner);

            $this->updateHtmlContent(($posted_data['postedData']['content'] ?? ''), $data);

            Database::getEM()->persist($data);
            Database::getEM()->flush();

            TopMessage::addInfo('The HTML code has been successfully added');
        }
    }
    /**
     * Update item
     *
     * REQUEST ACTION
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        $oldLocation = $this->getModelForm() && $this->getModelForm()->getModelObject() ? $this->getModelForm()->getModelObject()->getLocation() : null;
        $result   = $this->getModelForm()->performAction('modify');
        if (!Request::getInstance()->id) {
            $this->setReturnURL(
                $this->buildURL(
                    'banner_edit',
                    '',
                    ['id' => $this->getModelForm()->getModelObject()->getId()]
                )
            );
        }

        if ($result) {
            $this->rebuildViewListsAfterUpdate($oldLocation);
        }
    }

    /**
     * @throws \Exception
     */
    protected function doActionUpdateImages()
    {
        $list = new \QSL\Banner\View\ItemsList\Model\BannerSlides();

        $list->processQuick();

        $parallaxImage = Request::getInstance()->parallaxImage;
        $cnd = new CommonCell();
        $cnd->banner = $this->getBanner();

        foreach (Database::getRepo('QSL\Banner\Model\BannerSlide')->search($cnd) as $key) {
            $key->setParallaxImage(false);
        }

        $pImage = Database::getRepo('QSL\Banner\Model\BannerSlide')->find($parallaxImage);

        if ($pImage) {
            $pImage->setParallaxImage(true);
        }

        Database::getEM()->flush();

        TopMessage::addInfo('The banners list has been successfully updated');
    }


    /**
     * Update banner HTML content.
     *
     * @param Content                   $content       An instance of the class with the banner HTML content.
     * @param PersistentCollection|void $bannerContent An instance of the banner content class.
     *
     * @throws \Exception
     *
     * @return void
     */
    protected function updateHtmlContent($content, $bannerContent = null)
    {
        if (empty($bannerContent)) {
            $bannerContent = $this->getBannerContent();
        }

        $translation = $bannerContent->getTranslation();

        if (empty($translation) || empty($translation->getLabelId())) {
            $translation = new ContentTranslation();
            $translation->setOwner($bannerContent);
        }

        $translation->setContent($content);
        $entityManager = Database::getEM();
        $entityManager->persist($translation);
        $entityManager->flush();
    }


    /**
     * Update content
     *
     * @return void
     */
    protected function doActionUpdateContent()
    {
        $content = $this->getBannerContent();
        $data = Request::getInstance()->getNonFilteredData();


        $posted_data = (isset($data['banner_content_data']))
            ? $data['banner_content_data']
            : [];

        if (!empty($posted_data)) {
            $data = $posted_data[$this->getBannerContentId()];

            Database::getRepo('QSL\Banner\Model\Content')
                ->update($content, $data);

            $this->updateHtmlContent($data['content'] ?? '');

            TopMessage::addInfo('The banner content has been successfully updated');
        }
    }


    /**
     * Delete banner content
     *
     * @return void
     */
    protected function doActionDeleteContent()
    {
        $content = $this->getBannerContent();

        if ($content) {
            $content->getBanner()->getContents()->removeElement($content);

            Database::getEM()->remove($content);
            Database::getEM()->flush();

            TopMessage::addInfo(
                'The content has been deleted'
            );
        } else {
            TopMessage::addError(
                'The content has not been deleted'
            );
        }
    }

    /**
     * Update model and close banner info
     *
     * @return void
     */
    protected function doActionUpdateAndClose()
    {
        $oldLocation = $this->getModelForm() && $this->getModelForm()->getModelObject() ? $this->getModelForm()->getModelObject()->getLocation() : null;

        if ($this->getModelForm()->performAction('modify')) {
            $this->setReturnUrl(
                Converter::buildURL('banners_list')
            );

            $this->rebuildViewListsAfterUpdate($oldLocation);
        }
    }

    protected function rebuildViewListsAfterUpdate($oldLocation)
    {
        $model = $this->getModelForm();
        $model = $model ? $model->getModelObject() : null;

        if (
            Request::getInstance()->id
            && !empty($oldLocation)
            && $model
            && $oldLocation !== $model->getLocation()
        ) {
            $existingOneBanners = \XLite\Core\Database::getRepo('\XLite\Model\ViewList')->findByEntityId($model->getId()) ?: [];
            if (!empty($existingOneBanners)) {
                \XLite\Core\Database::getRepo('\XLite\Model\ViewList')->deleteInBatch($existingOneBanners);
            }

            $forceUpdateLocation = true;
        }
        // only for just added banners
        if (
            $this->getModelForm()->isValid()
            && (
                !Request::getInstance()->id
                || !empty($forceUpdateLocation)
            )
        ) {
            $controller = new \QSL\Banner\Controller\Admin\CacheManagement();
            $controller->rebuildViewLists();
        }
    }

    /**
     * @param \QSL\Banner\Model\Banner $banner
     * @return array
     */
    protected function getCategories(\QSL\Banner\Model\Banner $banner)
    {
        $data = new \Doctrine\Common\Collections\ArrayCollection();

        foreach ((array) $this->getPostedData('category_ids') as $categoryId) {
            $category = Database::getRepo('\XLite\Model\Category')->getCategory($categoryId);

            if ($category) {
                if (!$category->getBanners()->contains($banner)) {
                    $category->getBanners()->add($banner);
                }

                $data->add($category);
            }
        }

        return ['categories' => $data];
    }

    /**
     * Add part to the location nodes list
     *
     * @return void
     */
    protected function addBaseLocation()
    {
        parent::addBaseLocation();

        $this->addLocationNode(static::t('Banners'), $this->buildURL('banners_list'));
    }


    /**
     * Get model form class
     *
     * @return string
     */
    protected function getModelFormClass()
    {
        return 'QSL\Banner\View\Model\Banner';
    }
}
