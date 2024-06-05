<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\View;

/**
 * WidgetParamsSerializationException is thrown on attempt to serialize a complex widget param value (an object, for example). A possible solution could be implementing a custom serialization logic in your Dynamic widget and passing an already serialized value as a parameter.
 */
class WidgetParamsSerializationException extends \Exception
{
}
