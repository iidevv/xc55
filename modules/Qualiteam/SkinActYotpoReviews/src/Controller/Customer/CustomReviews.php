<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Controller\Customer;

use Qualiteam\SkinActYotpoReviews\Presenter\YotpoReviews;
use XCart\Container;
use XLite\Controller\Customer\Product;

class CustomReviews extends Product
{

    /**
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        parent::__construct($params);

        $this->setPresenterProduct();
    }

    /**
     * Check controller visibility
     *
     * @return boolean
     */
    protected function isVisible(): bool
    {
        return parent::isVisible()
            && $this->getProductId();
    }

    /**
     * @return void
     */
    protected function doNoAction(): void
    {
        $script = $this->getScript();
        $tag = $this->getYotpoWidgetTag();

        $page = <<<HTML
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
<meta name="robots" content="noindex,nofollow" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
$script
</head>
<body>
$tag
</body>
</html>
HTML;

        echo $page;
        exit(0);
    }

    /**
     * @return void
     */
    protected function setPresenterProduct(): void
    {
        $this->getPresenterProduct()?->setProduct(
            $this->getProduct()
        );
    }

    /**
     * @return \Qualiteam\SkinActYotpoReviews\Presenter\YotpoReviews|null
     */
    protected function getPresenterProduct(): ?YotpoReviews
    {
        return Container::getContainer()
            ?->get('yotpo.reviews.presenter.reviews');
    }

    /**
     * @return string
     */
    protected function getScript(): string
    {
        return sprintf(
            '<script type="text/javascript">
        (function e(){var e=document.createElement("script");e.type="text/javascript",e.async=true,e.src="//staticw2.yotpo.com/%s/widget.js";var t=document.getElementsByTagName("script")[0];t.parentNode.insertBefore(e,t)})();
</script>',
            Container::getContainer()?->get('yotpo.reviews.configuration')?->getAppKey(),
        );
    }

    /**
     * @return string
     */
    protected function getYotpoWidgetTag(): string
    {
        return "<div class='yotpo-widget-instance'
             id='yotpoReviewsWidget'
             data-yotpo-instance-id='{$this->getWidgetId()}'
             data-yotpo-product-id='{$this->getProductSku()}'
             data-yotpo-name='{$this->getProductName()}'
             data-yotpo-url='{$this->getProductUrl()}'
             data-yotpo-image-url='{$this->getProductImageUrl()}'
             data-yotpo-price='{$this->getProductPrice()}'
             data-yotpo-currency='{$this->getCurrency()}'></div>";
    }

    /**
     * @return string
     */
    protected function getWidgetId()
    {
        return $this->getPresenterConfig()->getReviewWidgetId();
    }

    /**
     * @return \Qualiteam\SkinActYotpoReviews\Presenter\Config
     */
    protected function getPresenterConfig()
    {
        return Container::getContainer()
            ?->get('yotpo.reviews.presenter.config');
    }

    /**
     * @return string
     */
    protected function getProductSku()
    {
        return $this->getPresenterProduct()?->getProductSku();
    }

    /**
     * @return string
     */
    protected function getProductName()
    {
        return $this->getPresenterProduct()?->getProductName();
    }

    /**
     * @return string
     */
    protected function getProductUrl()
    {
        return $this->getPresenterProduct()?->getProductUrl();
    }

    /**
     * @return string
     */
    protected function getProductImageUrl()
    {
        return $this->getPresenterProduct()?->getProductImageUrl();
    }

    /**
     * @return float
     */
    protected function getProductPrice()
    {
        return $this->getPresenterProduct()?->getProductPrice();
    }

    /**
     * @return string
     */
    protected function getCurrency()
    {
        return $this->getPresenterProduct()?->getCurrency();
    }
}