<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Pager;

use XLite\Core\Cache\ExecuteCachedTrait;

/**
 * Abstract pager class
 */
abstract class APager extends \XLite\View\RequestHandler\ARequestHandler
{
    use ExecuteCachedTrait;

    /**
     * Widget parameter names
     */
    public const PARAM_PAGE_ID                      = 'pageId';
    public const PARAM_ITEMS_COUNT                  = 'itemsCount';
    public const PARAM_ONLY_PAGES                   = 'onlyPages';
    public const PARAM_ITEMS_PER_PAGE               = 'itemsPerPage';
    public const PARAM_SHOW_ITEMS_PER_PAGE_SELECTOR = 'showItemsPerPageSelector';
    public const PARAM_LIST                         = 'list';
    public const PARAM_MAX_ITEMS_COUNT              = 'maxItemsCount';

    /**
     * Page short names
     */
    public const PAGE_FIRST    = 'first';
    public const PAGE_PREVIOUS = 'previous';
    public const PAGE_NEXT     = 'next';
    public const PAGE_LAST     = 'last';

    public const MAX_VISIBLE_PAGES = 5;

    /**
     * currentPageId
     * FIXME: due to old-style params mapping we cannot use the "pageId" name here:
     * it will be overridden by the "PARAM_PAGE_ID" request parameter
     *
     * @var integer
     */
    protected $currentPageId;

    /**
     * Cached pages
     *
     * @var array
     */
    protected $pages = null;


    /**
     * Return number of items per page
     *
     * @return integer
     */
    abstract protected function getItemsPerPageDefault();

    /**
     * Return number of pages to display
     *
     * @return integer
     */
    abstract protected function getPagesPerFrame();


    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/pager.less';
        $list[] = $this->getDir() . '/pager.css';

        if (!\XLite::isAdminZone()) {
            $list[] = 'common/grid-list.css';
        }

        return $list;
    }

    /**
     * Return CSS classes for parent block of pager (list-pager by default)
     *
     * @return string
     */
    public function getCSSClasses()
    {
        return 'list-pager';
    }

    /**
     * Return SQL condition with limits
     *
     * @param integer                $start Index of the first item on the page OPTIONAL
     * @param integer                $count Number of items per page OPTIONAL
     * @param \XLite\Core\CommonCell $cnd   Search condition OPTIONAL
     *
     * @return \XLite\Core\CommonCell
     */
    public function getLimitCondition($start = null, $count = null, \XLite\Core\CommonCell $cnd = null)
    {
        if ($start === null) {
            $start = $this->getStartItem();
        }

        if ($count === null) {
            $count = $this->getItemsPerPage();
        }

        if ($this->getMaxItemsCount()) {
            $count = max(
                0,
                min(
                    $count,
                    $this->getMaxItemsCount() - $start
                )
            );
        }

        return \XLite\Model\Repo\Base\Searchable::addLimitCondition($start, $count, $cnd);
    }

    /**
     * Get pages count
     *
     * @return integer
     */
    public function getPagesCount()
    {
        return $this->executeCachedRuntime(function () {
            return ceil($this->getItemsTotal() / $this->getItemsPerPage());
        });
    }

    /**
     * getItemsPerPage
     *
     * @return integer
     */
    public function getItemsPerPage()
    {
        $current = (int)$this->getParam(static::PARAM_ITEMS_PER_PAGE);

        return max(
            min($this->getItemsPerPageMax(), $current),
            $this->getItemsPerPageMin()
        );
    }


    /**
     * Return current list name
     *
     * @return string
     */
    protected function getListName()
    {
        return 'pager';
    }

    /**
     * getDir
     *
     * @return string
     */
    protected function getDir()
    {
        return 'pager';
    }

    /**
     * getPagerLabel
     *
     * @return label
     */
    protected function getPagerLabel()
    {
        return static::t('Products');
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/body.twig';
    }

    /**
     * getList
     *
     * @return \XLite\View\ItemsList\AItemsList
     */
    protected function getList()
    {
        return $this->getParam(static::PARAM_LIST);
    }

    /**
     * getItemsTotal
     *
     * @return integer
     */
    protected function getItemsTotal()
    {
        return min(
            $this->getParam(static::PARAM_ITEMS_COUNT),
            $this->getMaxItemsCount() ?: $this->getParam(static::PARAM_ITEMS_COUNT) + 1
        );
    }

    /**
     * Get maximum number of items to display in the list
     *
     * @return integer
     */
    protected function getMaxItemsCount()
    {
        return (int) $this->getParam(static::PARAM_MAX_ITEMS_COUNT);
    }

    /**
     * Return minimal possible items number per page
     *
     * @return integer
     */
    protected function getItemsPerPageMin()
    {
        return 1;
    }

    /**
     * Return maximal possible items number per page
     *
     * @return integer
     */
    protected function getItemsPerPageMax()
    {
        return 100;
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_PAGE_ID => new \XLite\Model\WidgetParam\TypeInt(
                'Page ID',
                1
            ),
            static::PARAM_ITEMS_COUNT => new \XLite\Model\WidgetParam\TypeInt(
                'Items number',
                0
            ),
            static::PARAM_ONLY_PAGES => new \XLite\Model\WidgetParam\TypeBool(
                'Only display pages list',
                false
            ),
            static::PARAM_ITEMS_PER_PAGE => new \XLite\Model\WidgetParam\TypeInt(
                'Items per page',
                $this->getItemsPerPageDefault(),
                true
            ),
            static::PARAM_SHOW_ITEMS_PER_PAGE_SELECTOR => new \XLite\Model\WidgetParam\TypeCheckbox(
                'Show pagination',
                true,
                true
            ),
            static::PARAM_LIST => new \XLite\Model\WidgetParam\TypeObject(
                'List object',
                null,
                false,
                '\XLite\View\ItemsList\AItemsList'
            ),
            static::PARAM_MAX_ITEMS_COUNT => new \XLite\Model\WidgetParam\TypeInt(
                'Maximum number of items to display in the list',
                0
            ),
        ];
    }

    /**
     * Define so called "request" parameters
     *
     * @return void
     */
    protected function defineRequestParams()
    {
        parent::defineRequestParams();

        $this->requestParams[] = static::PARAM_PAGE_ID;
        $this->requestParams[] = static::PARAM_ITEMS_PER_PAGE;
    }

    /**
     * Build page URL by page ID
     *
     * @param integer $pageId Page ID
     *
     * @return string
     */
    protected function buildURLByPageId($pageId)
    {
        return $this->getList()->getActionURL([static::PARAM_PAGE_ID => $pageId]);
    }

    /**
     * getFrameLength
     *
     * @return integer
     */
    protected function getFrameLength()
    {
        return min($this->getPagesPerFrame(), $this->getPagesCount());
    }

    /**
     * getFrameHalfLength
     *
     * @param boolean $shortPart Which part of frame to return OPTIONAL
     *
     * @return integer
     */
    protected function getFrameHalfLength($shortPart = true)
    {
        return call_user_func($shortPart ? 'floor' : 'ceil', $this->getFrameLength() / 2);
    }

    /**
     * getFrameStartPage
     *
     * @return integer
     */
    protected function getFrameStartPage()
    {
        $pageId = min(
            $this->getPageId() - $this->getFrameHalfLength(),
            $this->getPagesCount() - $this->getFrameLength()
        );

        return max(1, $pageId);
    }

    /**
     * Return ID of the first page
     *
     * @return integer
     */
    protected function getFirstPageId()
    {
        return 1;
    }

    /**
     * Return ID of the previous page
     *
     * @return integer
     */
    protected function getPreviousPageId()
    {
        return max(1, $this->getPageId() - 1);
    }

    /**
     * Return ID of the last page
     *
     * @return integer
     */
    protected function getLastPageId()
    {
        return (int)$this->getPagesCount();
    }

    /**
     * Return ID of the next page
     *
     * @return integer
     */
    protected function getNextPageId()
    {
        return min((int)$this->getPagesCount(), $this->getPageId() + 1);
    }

    /**
     * Return an array with information on the pages to be displayed
     *
     * @return array
     */
    protected function getPages()
    {
        if (!isset($this->pages)) {
            $this->pages = [];

            // Define the list of pages

            // Add "previous page" link
            $this->pages[] = [
                'type'  => 'previous-page',
                'num'   => $this->getPreviousPageId(),
                'title' => 'Previous page',
            ];

            // If we are able to display the whole list without omitting the pages then we do it
            if ($this->getPagesCount() > ($this->getFrameLength() + static::MAX_VISIBLE_PAGES)) {
                $this->buildFramedPageList();
            } else {
                $this->buildPlainPageList();
            }

            // Add "next page" link
            $this->pages[] = [
                'type'  => 'next-page',
                'num'   => $this->getNextPageId(),
                'title' => 'Next page',
            ];

            // Now prepare data for the view
            $this->preparePagesForView();
        }

        return $this->pages;
    }

    /**
     * Add some additional information for the pages inner structure which is specific for view
     *
     * @return void
     */
    protected function preparePagesForView()
    {
        foreach ($this->pages as $k => $page) {
            $num = $page['num'] ?? null;
            $type = $page['type'];

            $isItem = isset($num) && (in_array($type, ['item', 'first-page', 'last-page'], true));

            $isOmittedItems = $type === 'more-pages';
            $isSpecialItem = !$isItem && !$isOmittedItems;

            $isCurrent = isset($num) && $this->isCurrentPage($num);
            $isSelected = $isItem && $isCurrent;
            $isDisabled = $isSpecialItem && $isCurrent;

            $isActive = (!$isSelected && !$isOmittedItems && !$isDisabled && !\XLite::isAdminZone())
                || ($isSelected && \XLite::isAdminZone());

            if ($isItem || ($type === 'first-page') || ($type === 'last-page')) {
                $this->pages[$k]['text'] = $num;
            } elseif ($isOmittedItems) {
                $this->pages[$k]['text'] = '...';
            } elseif ($type === 'previous-page' && \XLite::isAdminZone()) {
                $this->pages[$k]['text'] = '&laquo;';
            } elseif ($type === 'next-page' && \XLite::isAdminZone()) {
                $this->pages[$k]['text'] = '&raquo;';
            } else {
                $this->pages[$k]['text'] = '&nbsp;';
            }

            $this->pages[$k]['page'] = !isset($num) ? null : 'page-' . $num;

            $needHrefForCustomer = isset($num)
                && !$isSelected
                && !$isDisabled
                && !\XLite::isAdminZone();

            $needHrefForAdmin = isset($num)
                && !$isOmittedItems
                && \XLite::isAdminZone();

            if ($needHrefForCustomer || $needHrefForAdmin) {
                $this->pages[$k]['href'] = $this->buildURLByPageId($num);
            }

            $classes = [
                'item'                   => $isItem || $isSpecialItem,
                'selected'               => $isSelected,
                'disabled'               => $isDisabled,
                'active'                 => $isActive,
                $this->pages[$k]['page'] => $isItem,
                $type                    => true,
            ];

            $css = [];

            foreach ($classes as $class => $enabled) {
                if ($enabled) {
                    $css[] = $class;
                }
            }

            $this->pages[$k]['classes'] = implode(' ', $css);
        }
    }

    /**
     * Build the full pager list without omitting the pages
     *
     * @return void
     */
    protected function buildPlainPageList()
    {
        for ($i = 1; $i <= $this->getPagesCount(); $i++) {
            $this->pages[] = [
                'type' => 'item',
                'num' => $i,
                'title' => '',
            ];
        }
    }

    /**
     * Build the pager list with one or two frames - omitted pages
     *
     * @return void
     */
    protected function buildFramedPageList()
    {
        $firstId = $this->getFirstPageId();
        $lastId = $this->getLastPageId();
        $pageId = $this->getPageId();
        $frameLength = $this->getFrameHalfLength(true);

        // Normalize the left and right positions of the frame
        $leftFrame  = max($firstId, $pageId - $frameLength);
        $rightFrame = min($pageId + $frameLength, $lastId - 1);

        // Flags whether the More info must we display the left frame with the full frame length
        $leftMore = ($firstId + 3) <= $leftFrame;
        $rightMore = ($rightFrame + 3) <= $lastId;

        if (!$leftMore && $rightMore) {
            // When only the right more info we display
            $leftFrame = $firstId + 1;
            $rightFrame = $leftFrame + $this->getFrameLength();
        } elseif ($leftMore && !$rightMore) {
            // When only the left more info we display the right frame with the full frame length
            $rightFrame = $lastId - 1;
            $leftFrame = $rightFrame - $this->getFrameLength();
        }

        // The first page is always displayed
        $this->pages[] = [
            'type' => 'first-page',
            'num' => $firstId,
            'title' => 'First page',
        ];

        // Defines whether to display the "More pages" on the left
        if ($leftMore) {
            $this->pages[] = [
                'type' => 'more-pages',
                'num' => null,
                'title' => '',
            ];
        }

        // Display the visible frame of pages
        for ($i = $leftFrame; $i <= $rightFrame; $i++) {
            $this->pages[] = [
                'type' => 'item',
                'num' => $i,
                'title' => '',
            ];
        }

        // Defines whether to display the "More pages" on the right
        if ($rightMore) {
            $this->pages[] = [
                'type' => 'more-pages',
                'num' => null,
                'title' => '',
            ];
        }

        // The last page is always displayed
        $this->pages[] = [
            'type' => 'last-page',
            'num' => $lastId,
            'title' => 'Last page',
        ];
    }

    /**
     * Check whether the page is currently selected
     *
     * @param integer $pageId ID of the page to check
     *
     * @return boolean
     */
    protected function isCurrentPage($pageId)
    {
        return $this->getPageId() == min(max(1, $pageId), $this->getLastPageId());
    }

    /**
     * Return ID of the current page
     *
     * @return integer
     */
    protected function getPageId()
    {
        if (!isset($this->currentPageId)) {
            $this->currentPageId = min((int)$this->getParam(static::PARAM_PAGE_ID), $this->getPagesCount());
        }

        return $this->currentPageId;
    }

    /**
     * Return index of the first item on the current page
     *
     * @return integer
     */
    protected function getStartItem()
    {
        return ($this->getPageId() - 1) * $this->getItemsPerPage();
    }

    /**
     * Get page begin record number
     *
     * @return integer
     */
    protected function getBeginRecordNumber()
    {
        return $this->getStartItem() + 1;
    }

    /**
     * Get page end record number
     *
     * @return integer
     */
    protected function getEndRecordNumber()
    {
        return min($this->getBeginRecordNumber() + $this->getItemsPerPage() - 1, $this->getItemsTotal());
    }

    /**
     * Check if pages list is visible or not
     *
     * @return boolean
     */
    protected function isPagesListVisible()
    {
        return 1 < $this->getPagesCount();
    }

    /**
     * isItemsPerPageVisible
     *
     * @return boolean
     */
    protected function isItemsPerPageVisible()
    {
        return !$this->getParam(static::PARAM_ONLY_PAGES);
    }

    /**
     * isItemsPerPageSelectorVisible
     *
     * @return boolean
     */
    protected function isItemsPerPageSelectorVisible()
    {
        return $this->getParam(static::PARAM_SHOW_ITEMS_PER_PAGE_SELECTOR);
    }

    /**
     * isSearchTotalVisible
     *
     * @return boolean
     */
    protected function isSearchTotalVisible()
    {
        return \XLite\Core\Request::getInstance()->target === 'order_list';
    }

    /**
     * isVisible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && ($this->isPagesListVisible() || $this->isItemsPerPageVisible() || $this->isSearchTotalVisible());
    }

    /**
     * isVisible bottom
     *
     * @return boolean
     */
    protected function isVisibleBottom()
    {
        return $this->isVisible();
    }

    /**
     * Get specific for items list 'More' link URL
     *
     * @return string
     */
    protected function getMoreLink()
    {
        return $this->getList()->getMoreLink();
    }

    /**
     * Get specific for items list 'More' link title
     *
     * @return string
     */
    protected function getMoreLinkTitle()
    {
        return $this->getList()->getMoreLinkTitle();
    }

    /**
     * Link session cell name with related list
     *
     * @return string
     */
    public function getSessionCell()
    {
        $cell = parent::getSessionCell() . '_' . \XLite::getController()->getPagerSessionCell();

        return $cell;
    }

    /**
     * Should we use cache for pageId
     *
     * @return boolean
     */
    protected function isSavedPageId()
    {
        return true;
    }

    /**
     * Unset 'pageId' value from saved parameters
     *
     * @param string $param Parameter name
     *
     * @return mixed
     */
    protected function getSavedRequestParam($param)
    {
        $result = null;

        if ($param !== static::PARAM_PAGE_ID || $this->isSavedPageId()) {
            $result = parent::getSavedRequestParam($param);
        }

        return $result;
    }
}
