<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Presenter;

use XCart\Container;

class JSYotpoReviews
{
    /**
     * @return string
     */
    public function getYotpoStarsScript(): string
    {
        return sprintf(
            '<script type="text/javascript">
(function e(){var e=document.createElement("script");e.type="text/javascript",e.async=true,e.src="//staticw2.yotpo.com/%s/widget.js";var t=document.getElementsByTagName("script")[0];t.parentNode.insertBefore(e,t)})();
</script>',
            Container::getContainer()?->get('yotpo.reviews.configuration')?->getAppKey(),
        );
    }
}