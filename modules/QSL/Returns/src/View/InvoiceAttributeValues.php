<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Returns\View;

use XCart\Extender\Mapping\ListChild;

/**
 * Invoice item attribute values
 *
 * @ListChild (list="order_return.item.name", weight="50")
 * @ListChild (list="order_return.item.name", weight="20", zone="admin")
 * @ListChild (list="order_return.item.name", weight="50", interface="mail", zone="common")
 */
class InvoiceAttributeValues extends \XLite\View\InvoiceAttributeValues
{
}
