<?php
// phpcs:ignoreFile
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

use Includes\Utils\Module\Manager;
use XLite\Core\Exception\FatalException;

/**
 * Flexy compiler
 */
class FlexyCompiler extends \XLite\Base\Singleton
{
    /**
     * Tag to define arrays in templates
     */
    public const TAG_ARRAY = '_ARRAY_';

    public const PHP_OPEN = '<?php';
    public const PHP_CLOSE = '?>';

    /**
     * Template source code
     *
     * @var string
     */
    protected $source = null;

    /**
     * Template file name
     *
     * @var string
     */
    protected $file = null;

    /**
     * List of URLs to rewrite
     *
     * @var array
     */
    protected $urlRewrite = [];

    /**
     * Image URL output type
     *
     * @var string
     */
    protected $imageURLOutputType = \XLite\Core\Layout::WEB_PATH_OUTPUT_URL;

    /**
     * CCompile directory (local cache)
     *
     * @var string
     */
    protected $compileDir;

    public $substitutionStart = [];
    public $substitutionEnd = [];
    public $substitutionValue = [];

    public function parse($file)
    {
        $file = str_replace('/', LC_DS, $file);

        $this->init($file);

        $this->offset = 0;
        $this->stack = [];
        $this->tokens = [];
        $this->widgetNames = [];
        $this->errorMessage = '';
        $this->html();
        $this->substitutionStart = [];
        $this->substitutionEnd = [];
        $this->substitutionValue = [];

        return trim($this->postprocess()) . "\n";
    }

    function savePosition($offs = 0)
    {
        array_push($this->stack, $this->offset + $offs);
        array_push($this->stack, count($this->tokens));
    }
    function rollback()
    {
        $count = array_pop($this->stack);
        array_splice($this->tokens, $count);
        $this->offset = array_pop($this->stack);
        return false;
    }
    function commit()
    {
        array_pop($this->stack);
        array_pop($this->stack);
        return true;
    }
    function startOffset()
    {
        return $this->stack[count($this->stack) - 2];
    }
    function error($message)
    {
        // count \n
        $line = $col = 1;
        for ($i = 0; $i < $this->offset; $i++) {
            if (substr($this->source, $i, 1) == "\n") {
                $line++;
                $col = 0;
            }
            $col++;
        }
        throw new FatalException("File $this->file, line $line, col $col: $message");
    }
    function isEos()
    {
        return $this->offset >= strlen($this->source) || $this->errorMessage;
    }

    // html ::= ([text] tag | [text] comment | [text] flexy)* [text]
    protected function html()
    {
        while ($this->phptag() || $this->tag() || $this->comment() || $this->flexyComment() || $this->flexy() || $this->anyChar()) {
        }
        return true;
    }

    // tag ::= open-tag | close-tag | open-close-tag
    // open-tag ::= '<' tagname (space+ attribute-definition)* space* '>'
    // open-close-tag ::= '<' tagname (space+ attribute-definition)* space* '/>'
    // close-tag ::= '</' tagname  '>'
    protected function tag()
    {
        if ($this->char('<')) {
            $this->savePosition(-1);
            $n = count($this->tokens);
            if ($this->char('/')) {
                $this->tokens[] = ["type" => "close-tag", "start" => $this->startOffset()];
                if (!$this->tagname()) {
                    return $this->rollback();
                }
                if (!$this->char('>')) {
                    return $this->rollback();
                }
            } else {
                $this->tokens[] = ["type" => "tag", "start" => $this->startOffset()];
                if (!$this->tagname()) {
                    return $this->rollback();
                }
                while ($this->space() && $this->attribute_definition()) {
                }
                if ($this->char('/')) {
                    $this->tokens[$n]['type'] = "open-close-tag";
                }
                if (!$this->char('>')) {
                    return $this->rollback();
                }
            }
            $this->tokens[$n]['end'] = $this->offset;
            return $this->commit();
        }
    }

    // attribute-definition ::= attributename [ '=' attribute-value ]
    // attribute-value ::= '\'' attribute-text '\'' | '"' attribute-text '"' | [^ \t\n\r/>]+
    protected function attribute_definition()
    {
        $this->savePosition();
        $i = count($this->tokens);
        $this->tokens[] = ["type" => "attribute", "start" => $this->offset];
        if ($this->attributename()) {
            if ($this->char('=')) {
                // read attribute value
                $n = count($this->tokens);
                if ($this->char('\'')) {
                    $this->tokens[] = ["type" => "attribute-value", "start" => $this->offset];
                    while (!$this->char('\'') && !$this->isEos()) {
                        $this->attribute_text();
                    }
                    $this->tokens[$n]['end'] = $this->offset - 1;
                } elseif ($this->char('"')) {
                    $this->tokens[] = ["type" => "attribute-value", "start" => $this->offset];
                    while (!$this->char('"') && !$this->isEos()) {
                        $this->attribute_text();
                    }
                    $this->tokens[$n]['end'] = $this->offset - 1;
                } else {
                    $this->tokens[] = ["type" => "attribute-value", "start" => $this->offset];
                    while ($this->notChars(" \t\n\r/>") && !$this->isEos()) {
                    }
                    $this->tokens[$n]['end'] = $this->offset;
                }
                if ($this->isEos()) { // unexpected end of file
                    return $this->error('unexpected end of file');
                }
            }
            $this->tokens[$i]['end'] = $this->offset;
            return $this->commit();
        } else {
            return $this->rollback();
        }
    }
    function attribute_text()
    {
        if ($this->char('{')) { // skip till closing }
            while (!$this->char('}') && $this->anyChar()) {
            }
            return true;
        } else {
            return $this->anyChar();
        }
    }
    // comment ::= '<!--' text '-->'
    function comment()
    {
        if ($this->offset < strlen($this->source) && substr($this->source, $this->offset, 1) == '<') {
            if (substr($this->source, $this->offset, 4) == '<!--') {
                $pos = strpos($this->source, '-->', $this->offset + 4);
                if ($pos === false) {
                    return $this->error("Comment is not closed with -->");
                }
                $this->offset = $pos + 3;
                return true;
            }
        }
        return false;
    }

    // php tag ::= '< ?' php code '? >'
    function phptag()
    {
        if ($this->offset < strlen($this->source) && substr($this->source, $this->offset, 1) == '<') {
            if (substr($this->source, $this->offset, 2) == '<?') {
                throw new FatalException("&lt;?php&gt; tags are not allowed in templates");
            }
        }
        return false;
    }


    // flexy ::= '{' flexy-text '}'
    function flexy()
    {
        if ($this->char('{')) { // skip till closing }
            $this->savePosition(-1);
            if ($this->notChars(" \t\n\r}")) {
                $this->tokens[] = ["type" => "flexy", "start" => $this->offset - 2];
                while (!$this->char('}')) {
                    if (!$this->anyChar()) {
                        $this->error("No closing }");
                    }
                }
                $this->tokens[count($this->tokens) - 1]['end'] = $this->offset;
                return $this->commit();
            } else {
                return $this->rollback();
            }
        }
    }

    function flexyComment()
    {
        if ($this->offset < strlen($this->source) && substr($this->source, $this->offset, 1) == '{') {
            if (substr($this->source, $this->offset, 2) == '{*') {
                $this->tokens[] = ["type" => "flexy", "start" => $this->offset];
                $pos = strpos($this->source, '*}', $this->offset + 2);
                if ($pos === false) {
                    return $this->error("Comment is not closed with *}");
                }
                $this->offset = $pos + 2;
                $this->tokens[count($this->tokens) - 1]['end'] = $this->offset;
                return true;
            }
        }
        return false;
    }

    // space ::= ' ' | '\t' | '\n' | '\r'
    function space()
    {
        $result = false;
        while ($this->char(' ') || $this->char("\n") || $this->char("\r") || $this->char("\t")) {
            $result = true;
        }
        return $result;
    }

    protected function char($c)
    {
        if (substr($this->source, $this->offset, 1) === $c) {
            $this->offset++;

            return true;
        }

        return false;
    }

    function notChars($str)
    {
        if (strpos($str, substr($this->source, $this->offset, 1)) === false) {
            $this->offset ++;
            return true;
        }
        return false;
    }
    function anyChar()
    {
        if ($this->isEos()) {
            return false;
        }
        $this->offset ++;
        return true;
    }
    function tagname()
    {
        $tagname = $c = '';
        do {
            $tagname .= $c;
            $c = substr($this->source, $this->offset++, 1);
        } while ($c >= 'a' && $c <= 'z' || $c >= 'A' && $c <= 'Z' || $c >= '0' && $c <= '9' || $c == '_' || $c == ':' || $c == '-');
        $this->offset--;
        if (strlen($tagname)) {
            $this->tokens[count($this->tokens) - 1]['name'] = $tagname;
            return true;
        }
        return false;
    }
    function attributename()
    {
        return $this->tagname();
    }

    // Flexy substitutions
    function postprocess()
    {
        for ($i = 0; $i < count($this->tokens); $i++) {
            $token = $this->tokens[$i];

            if ($token['type'] == "tag" || $token['type'] == "open-close-tag") {
                if ($this->findAttr($i + 1, 'if', $pos) && (strcasecmp($token['name'], 'widget') !== 0)) {
                    if ($this->findClosingTag($i, $pos1)) {
                        $expr = $this->flexyCondition($this->getTokenText($pos + 1));
                        $this->subst($token['start'], 0, static::PHP_OPEN . ' if (' . $expr . '): ' . static::PHP_CLOSE);
                        $this->subst($this->tokens[$pos]['start'], $this->tokens[$pos]['end'], '');
                        $this->subst($this->tokens[$pos1]['end'] - 1, $this->tokens[$pos1]['end'], '>' . static::PHP_OPEN . ' endif; ' . static::PHP_CLOSE);
                    }
                } elseif ($this->findAttr($i + 1, "iff", $pos)) {
                    $expr = $this->flexyCondition($this->getTokenText($pos + 1));
                    $this->subst($token['start'], 0, static::PHP_OPEN . " if ($expr){" . static::PHP_CLOSE);
                    $this->subst($this->tokens[$pos]['start'], $this->tokens[$pos]['end'], '');
                    $this->subst($this->tokens[$i]['end'] - 1, $this->tokens[$i]['end'], '>' . static::PHP_OPEN . ' }' . static::PHP_CLOSE);
                } elseif ($this->findAttr($i + 1, "foreach", $pos)) {
                    if ($this->findClosingTag($i, $pos1)) {
                        [$expr,$k,$forvar] = $this->flexyForeach($this->getTokenText($pos + 1));
                        $exprNumber = $forvar . 'ArraySize';
                        $exprCounter = $forvar . 'ArrayPointer';
                        $this->subst($token['start'], 0, static::PHP_OPEN . " \$$forvar = isset(\$this->$forvar) ? \$this->$forvar : null; \$_foreach_var = $expr; if (isset(\$_foreach_var)) { \$this->$exprNumber=count(\$_foreach_var); \$this->$exprCounter=0; } if (isset(\$_foreach_var)) foreach (\$_foreach_var as $k){ \$this->$exprCounter++; " . static::PHP_CLOSE);
                        $this->subst($this->tokens[$pos]['start'], $this->tokens[$pos]['end'], '');
                        $this->subst($this->tokens[$pos1]['end'] - 1, $this->tokens[$pos1]['end'], ">\n" . static::PHP_OPEN . " } \$this->$forvar = \$$forvar; " . static::PHP_CLOSE);
                    } else {
                        $this->error('No closing tag for foreach');
                    }
                }

                // Boolean-based attribute process
                // :FIXME: <... disabled="disabled" checked="checked" ... /> does not work

                $boolAttribute = false;

                if ($this->findAttr($i + 1, 'selected', $pos)) {
                    $boolAttribute = 'selected';
                } elseif ($this->findAttr($i + 1, 'checked', $pos)) {
                    $boolAttribute = 'checked';
                } elseif ($this->findAttr($i + 1, 'disabled', $pos)) {
                    $boolAttribute = 'disabled';
                }

                if (
                    $boolAttribute
                    && isset($this->tokens[$pos + 1]['type'])
                    && $this->tokens[$pos + 1]['type'] == 'attribute-value'
                ) {
                    $expr = $this->flexyCondition($this->getTokenText($pos + 1));
                    $this->subst(
                        $this->tokens[$pos]['start'],
                        $this->tokens[$pos]['end'],
                        static::PHP_OPEN . ' if (' . $expr . ') { echo \'' . $boolAttribute . '="' . $boolAttribute . '"\'; } ' . static::PHP_CLOSE
                    );
                }

                $this->parseWidget($token, $i);
                $this->parseList($token, $i);
            } elseif ($token['type'] == 'close-tag' && $this->isDumpCloseTag($token)) {
                $this->subst($token['start'], $token['end'], '');
            }

            if ($token['type'] == "flexy") {
                $expr = $this->flexyEcho($this->getTokenText($i));
                $this->subst($token['start'], $token['end'], $expr);
            } elseif ($token['type'] == "attribute") {
                if (!strcasecmp($token['name'], 'src') || !strcasecmp($token['name'], 'background')) {
                    $rewriteData = $this->rewriteURL($this->getTokenText($i + 1));

                    if ($rewriteData) {
                        $this->subst(
                            $this->tokens[$i + 1]['start'] + $rewriteData[0],
                            $this->tokens[$i + 1]['start'] + $rewriteData[1],
                            $rewriteData[2]
                        );
                    }
                }
            } elseif ($token['type'] == "attribute-value") {
                $str = $this->getTokenText($i);
                // find all {...}
                $pos = 0;
                while (($pos = strpos($str, "{", $pos)) !== false) {
                    $pos1 = strpos($str, "}", $pos);
                    if ($pos1 !== false) {
                        $echo = $this->flexyEcho(substr($str, $pos, $pos1 - $pos + 1));
                        $this->subst($token['start'] + $pos, $token['start'] + $pos1 + 1, $echo);
                    } else {
                        break;
                    }
                    $pos = $pos1;
                }
            }
        }

        return $this->substitute();
    }

    /**
     * Check - token is dump close tag or not
     *
     * @param array $token Token
     *
     * @return boolean
     */
    protected function isDumpCloseTag(array $token)
    {
        return in_array($token['name'], ['widget', 'list']);
    }

    /**
     * Get template info
     *
     * @return array
     */
    protected function getTemplateInfo()
    {
        $skin = preg_replace('/^([a-x0-9_]+)[^a-x0-9_].+$/Ssi', '\1', substr($this->file, strlen(LC_DIR_SKINS)));
        $template = $skin == 'common'
            ? substr($this->file, strlen(LC_DIR_SKINS) + strlen($skin) + 1)
            : substr($this->file, strlen(LC_DIR_SKINS) + strlen($skin) + 4);

        return [$skin, $template];
    }

    protected function unsetAttributes(array &$attrs, array $keys)
    {
        foreach ($keys as $key) {
            unset($attrs[$key]);
        }
    }

    protected function getAttributesList(array $attrs)
    {
        $result = [];

        if (isset($attrs['mode'])) {
            $result[] = '\'mode\' => array(\'' . implode('\', \'', explode(',', $attrs['mode'])) . '\')';
            unset($attrs['mode']);
        }

        foreach ($attrs as $key => $value) {
            $result[] = '\'' . $key . '\' => ' . $this->flexyAttribute($value);
        }

        return 'array(' . implode(', ', $result) . ')';
    }

    function widgetDisplayCode(array $attrs, $target, $module, $name)
    {
        $result = '';

        if (!isset($module) || Manager::getRegistry()->isModuleEnabled($module)) {
            $class = isset($attrs['class']) ? $this->flexyAttribute($attrs['class'], false) : null;

            $arguments  = isset($class) ? $this->flexyAttribute($attrs['class']) : (isset($name) ? 'null' : '');
            $arguments .= isset($name) ? ', ' . $this->flexyAttribute($name) : '';

            $conditions = [];

            if (isset($target)) {
                $target = str_replace(',', '\',\'', preg_replace('/[^\w,]+/', '', $target));
            } elseif (isset($class) && preg_match('/^\\\\?XLite\\\\/i', $class)) {
                $target = implode('\',\'', $class::getAllowedTargets());
            }

            if (!empty($target)) {
                $conditions[] = '$this->isDisplayRequired(array(\'' . $target . '\'))';
            }

            if (!empty($attrs['mode'])) {
                $modes = str_replace(',', '\',\'', preg_replace('/[^\w,]+/', '', $attrs['mode']));
                $conditions[] = '$this->isDisplayRequiredForMode(array(\'' . $modes . '\'))';
            }

            if (isset($attrs['IF'])) {
                $attrs['IF'] = $this->flexyCondition($attrs['IF']);
                if (!empty($conditions)) {
                    $attrs['IF'] = '(' . $attrs['IF'] . ')';
                }
                $conditions[] = $attrs['IF'];
            }

            $this->unsetAttributes($attrs, ['IF', 'FOREACH', 'class', 'mode']);

            if (empty($arguments) && (count($attrs) == 1) && isset($attrs['template'])) {
                $result .= '$this->display(' . $this->flexyAttribute($attrs['template'])  . ');';
            } else {
                $result .= '$this->getWidget('
                    . (empty($attrs) ? (empty($arguments) ? '' : 'array()') : $this->getAttributesList($attrs))
                    . (empty($arguments) ? '' : ', ' . $arguments) . ')->display();';
            }


            if (!empty($conditions)) {
                $result = 'if (' . implode(' && ', $conditions) . '):' . "\n" . '  ' . $result . "\n" . 'endif;';
            }
        }

        return $result;
    }

    function substitute()
    {
        // sort substitutions
        array_multisort($this->substitutionStart, $this->substitutionEnd, $this->substitutionValue);
        $lastEnd = 0;
        $result = '';
        for ($i = 0; $i < count($this->substitutionStart); $i++) {
            if ($lastEnd <= $this->substitutionStart[$i]) {
                $result .= substr($this->source, $lastEnd, $this->substitutionStart[$i] - $lastEnd);
                $result .= $this->substitutionValue[$i];
                $lastEnd = $this->substitutionEnd[$i];
            }
        }
        $result .= substr($this->source, $lastEnd);
        return $result;
    }

    /**
     * Rewrite URL
     *
     * @param string $url Short URL
     *
     * @return boolean|array
     */
    protected function rewriteURL($url)
    {
        $result = false;

        foreach ($this->urlRewrite as $find => $replace) {
            $len = strlen($find);

            if (strncmp($url, $find, $len) === 0) {
                if (is_callable($replace)) {
                    $result = call_user_func($replace, $url, $len);
                } else {
                    $result = [0, $len, $replace];
                }

                break;
            }
        }

        return $result;
    }

    /**
     * Rewrite image URL
     *
     * @param string  $url    Image short URL
     * @param integer $length Replace part length
     *
     * @return string
     */
    protected function rewriteImageURL($url, $length)
    {
        $newURL = \XLite\Singletons::$handler->layout->getResourceWebPath($url, $this->imageURLOutputType);

        return $newURL
            ? [0, strlen($url), $newURL]
            : [0, $length, \XLite\Singletons::$handler->layout->prepareSkinURL('images', $this->imageURLOutputType)];
    }

    function subst($start, $end, $value)
    {
        if ($end == 0) {
            $end = $start;
        }
        $this->substitutionStart[] = $start;
        $this->substitutionEnd[] = $end;
        $this->substitutionValue[] = $value;
    }
    function findAttr($offset, $attr, &$pos)
    {
        $pos = $offset;
        while ($pos < count($this->tokens) && ($this->tokens[$pos]['type'] == "attribute" || $this->tokens[$pos]['type'] == "attribute-value")) {
            if ($this->tokens[$pos]['type'] == "attribute"  && !strcasecmp($this->tokens[$pos]['name'], $attr)) {
                return true;
            }
            $pos++;
        }
        return false;
    }
    function findClosingTag($i, &$pos)
    {
        $pos = $i;
        $stack = [];
        while ($pos < count($this->tokens)) {
            if ($this->tokens[$pos]['type'] == "tag" || $this->tokens[$pos]['type'] == "open-close-tag") {
                array_push($stack, $this->tokens[$pos]['name']);
            }
            if ($this->tokens[$pos]['type'] == "close-tag" || $this->tokens[$pos]['type'] == "open-close-tag") {
                $k = count($stack) - 1;
                while ($k >= 0 && strcasecmp($stack[$k], $this->tokens[$pos]['name'])) {
                    $k--;
                }
                if ($k == 0) {
                    return true;
                }
                if ($k >= 0) {
                    // opening tag is found
                    array_splice($stack, $k);
                }
            }
            $pos++;
        }
        return false;
    }
    function getTokenText($n)
    {
        $t = $this->tokens[$n];
        $this->offset = $t['start'];
        return substr($this->source, $t['start'], $t['end'] - $t['start']);
    }
    function flexyCondition($str)
    {
        $str = $this->removeBraces($str);
        $this->condition = '';
        if (substr($str, 0, 1) == '!') {
            $str = substr($str, 1);
            $res = $this->flexyExpression($str);
            $not = "!";
        } else {
            $res = $this->flexyExpression($str);
            $not = "";
        }
        if ($this->condition) {
            $res = "$this->condition && $res";
        }
        if ($not) {
            return "!($res)";
        } else {
            return $res;
        }
    }
    function flexyEcho($str)
    {
        if (substr($str, 0, 9) == '{foreach:') {
            [$expr,$k,$forvar] = $this->flexyForeach(substr($str, 9));
            $exprNumber = "$forvar" . "ArraySize";
            $exprCounter = "$forvar" . "ArrayPointer";
            return static::PHP_OPEN . " \$_foreach_var = $expr; if (isset(\$_foreach_var)) { \$this->$exprNumber=count(\$_foreach_var); \$this->$exprCounter=0; } if (isset(\$_foreach_var)) foreach (\$_foreach_var as $k){ \$this->$exprCounter++; " . static::PHP_CLOSE;
        }
        if (substr($str, 0, 4) == '{if:') {
            $expr = $this->flexyCondition(substr($str, 4));
            return static::PHP_OPEN . " if ($expr){" . static::PHP_CLOSE;
        }
        if (substr($str, 0, 8) == '{elseif:') {
            $expr = $this->flexyCondition(substr($str, 8));
            return static::PHP_OPEN . " }elseif ($expr){" . static::PHP_CLOSE;
        }
        if ($str == '{end:}') {
            return static::PHP_OPEN . " }" . static::PHP_CLOSE;
        }
        if ($str == '{else:}') {
            return static::PHP_OPEN . " }else{ " . static::PHP_CLOSE;
        }
        if (substr($str, 0, 2) == "{*") {
            $str = '';
            return "";
        }
        $this->condition = '';

        $expr = $this->flexyExpression($str);

        switch ($str) {
            case ':h':  // will display variable "as is"
                break;

            case '':    // default display
                $expr = 'func_htmlspecialchars(' . $expr . ')';
                break;

            case ':r':
                $expr = "str_replace('\"', '&quot;',$expr)";
                break;

            case ':u':
                $expr = "urlencode($expr)";
                break;

            case ':t':
                $expr = "htmlentities($expr)";
                break;

            case ':p':
                // :TODO: refactor.
                // \XLite\Core\Converter::formatPrice has been removed.
                // Right now use formatPrice() common viewer method instead
                //$expr = '\XLite\Core\Converter::formatPrice(' . $expr . ')';
                break;

            case ':s':
                // Do nothing - silent
                break;

            case ':b':
                // Replace new-line with <br>
                $expr = 'nl2br(' . $expr . ')';
                break;

            default:
                $wrongModifier = true;
                if (substr($str, 0, 1) == ':') {
                    $func = substr($str, 1);
                    $expr = '$this->flexyModifierCall(\'' . $func . '\', ' . $expr . ')';
                    $wrongModifier = false;
                } else {
                    $this->error("Unknown modifier '$str'");
                }
        }

        if ($str !== ':s') {
            $expr = 'echo ' . $expr;
        }

        if ($this->condition) {
            $expr = 'if (' . $this->condition . ') ' . $expr;
        }

        return static::PHP_OPEN . ' ' . $expr . '; ' . static::PHP_CLOSE;
    }

    function flexyExpression(&$str)
    {
        $str = $this->removeBraces($str);
        if (substr($str, 0, 1) == '!') { // NOT
            $str = substr($str, 1);
            return '!(' . $this->flexyExpression($str) . ')';
        }
        $result = $this->flexySimpleExpression($str);

        if (substr($str, 0, 1) == '=') { // comparision
            $str = substr($str, 1);
            $result .= '==' . $this->flexyExpression($str);
        }
        if (substr($str, 0, 1) == '&') { // AND
            $str = substr($str, 1);
            $result .= '&&' . $this->flexyExpression($str);
        }
        if (substr($str, 0, 1) == '|') { // OR
            $str = substr($str, 1);
            $result .= '||' . $this->flexyExpression($str);
        }
        if (substr($str, 0, 1) == '^') { // array element assignment
            $str = substr($str, 1);
            $result .= '=>' . $this->flexyExpression($str);
        }

        return $result;
    }

    function flexySimpleExpression(&$str)
    {
        if (substr($str, 0, 1) == '#') {
            // find next #
            $pos = strpos($str, '#', 1);

            if ($pos === false) {
                $this->error('No closing #');
            }

            // FIXME find the better way to prevent adding slashes to '"' character.
            $result = '\'' . str_replace('\"', '"', addslashes(substr($str, 1, $pos - 1))) . '\'';

            $str = substr($str, $pos + 1);

            return $result;
        }

        if (substr($str, 0, 1) == "%") {
            // find next %
            $pos = strpos($str, "%", 1);
            if ($pos === false) {
                $this->error("No closing %");
            }
            $result = substr($str, 1, $pos - 1);
            $str = substr($str, $pos + 1);
            return $result;
        }
        if (substr($str, 0, 1) >= '0' && substr($str, 0, 1) <= '9' || substr($str, 0, 1) == '-' || substr($str, 0, 1) == '.') { // numeric constant
            $len = strspn($str, '0123456789-.');
            $result = substr($str, 0, $len);
            $str = substr($str, $len);
            return $result;
        }

        $len = strcspn($str, '=&|,)(:^');
        if ($len < strlen($str) && substr($str, $len, 1) == '(') { // method call
            $token  = substr($str, 0, $len);
            $method = (($dotPos = strrpos($token, '.')) !== false) ? substr($token, $dotPos + 1) : $token;
            $field  = substr($str, 0, $dotPos);

            if ($method === static::TAG_ARRAY) {
                $result = 'array';
            } else {
                $result = '$this->' . (($dotPos === false) ? '' : 'get' . (strrpos($field, '.') ? 'Complex' : '') . '(\'' . $field . '\')->') . $method;
            }

            $str = substr($str, $len);
            $params = [];

            if (substr($str, 1, 1) != ')') {
                while (substr($str, 0, 1) != ')') {
                    $str = substr($str, 1); // eat , or (
                    if (strlen($str) == 0) {
                        $this->error("No closing )");
                    }
                    $params[] = $this->flexyExpression($str);
                }
                $str = substr($str, 1); // eat )
            } else {
                $str = substr($str, 2); // eat ()
            }
            return $result . '(' . implode(',', $params) . ')';
        }
        if ($len) {
            // field
            $field  = substr($str, 0, $len);
            $result = '$this->get' . (strpos($field, '.') ? 'Complex' : '') . '(\'' . $field . '\')';
        } else {
            $result = '';
        }
        $str = substr($str, $len);
        return $result;
    }

    function flexyAttribute($str, $addQuotes = true)
    {
        if ($str === '') {
            return '\'\'';
        }
        // find all {..} in $str and replace with flexyExpression()
        $result = "";
        $find = ["'", '&quot;'];
        $replace = ["\'", '"'];
        while (strlen($str)) {
            if (substr($str, 0, 1) == "{") {
                $pos = strpos($str, "}");
                if ($pos === false) {
                    $this->error("} not found");
                    return "";
                }
                $tmp = substr($str, 0, $pos + 1);
                $s = $this->flexyExpression($tmp);
                $str = substr($str, $pos + 1);
            } else {
                $pos = strpos($str, "{");
                if ($pos === false) {
                    $pos = strlen($str);
                }
                $s = str_replace($find, $replace, substr($str, 0, $pos));
                if ($addQuotes) {
                    $s = '\'' . $s . '\'';
                }
                $str = substr($str, $pos);
            }
            if ($result === "") {
                $result = $s;
            } else {
                $result .= "." . $s; // catenation
            }
        }
        return $result;
    }

    function flexyForeach($str)
    {
        $expr = $this->flexyExpression($str);
        if (substr($str, 0, 1) != ',') {
            $this->error('No comma in foreach expression');
        }
        $str = substr($str, 1);
        $list = explode(",", $str);
        if (count($list) == 2) {
            [$k, $v] = $list;
            $forvar = $v;
            $key = '$this->' . $k . ' => $this->' . $forvar;
        } else {
            $forvar = $list[0];
            $key = '$this->' . $forvar;
        }

        return [$expr, $key, $forvar];
    }

    function removeBraces($str)
    {
        return rtrim(ltrim($str, '{'), '}');
    }

    protected function getXliteFormIDText()
    {
        static $formId = null;
        if (!isset($formId)) {
            $formId = \XLite::getFormId();
        }

        return $formId;
    }

    /**
     * @todo: remove since we do not use it anymore.
     * Use the Form classes instead
     *
     * @staticvar null $script_start
     * @param type $tokenIndex
     */
    protected function attachFormID($tokenIndex)
    {
        if (\XLite::isAdminZone()) {
            $token = $this->tokens[$tokenIndex];
            $token['name'] = empty($token['name']) ? '' : strtolower($token['name']);

            // sign each form with generated form_id
            if ($token['type'] == 'tag' && $token['name'] == 'form') {
                $genFormId = $this->getXliteFormIDText();
                $this->subst(
                    $token['end'],
                    0,
                    '<fieldset><input type="hidden" name="' . \XLite::FORM_ID . '" value="' . $genFormId . '" /></fieldset>'
                );
            }

            // attach form_id to all links inside attributes (in case they contain javascript links)
            if ($token['type'] == 'attribute-value') {
                $str = $this->getTokenText($tokenIndex);
                $this->_addFormIdToActions($str, $token['start']);
            }

            // attach form_id to all links inside scripts
            static $script_start = null;
            if ($token['type'] == 'tag' && $token['name'] == 'script') {
                $script_start = $token['end'];
            } elseif ($token['type'] == 'close-tag' && $token['name'] == 'script' && isset($script_start)) {
                $script_end = $token['start'];
                $script_body = substr($this->source, $script_start, $script_end - $script_start);
                $this->_addFormIdToActions($script_body, $script_start);
                $script_start = null;
            }
        }
    }

    protected function _addFormIdToActions($text, $text_start)
    {
        $blocks = [];
        $search_text = 'action=';
        $prev_pos = 0;
        while ($pos = strpos($text, $search_text, $prev_pos)) {
            $blocks[] = [
                'start' => $prev_pos,
                'end'   => $pos + strlen($search_text),
                'body'  => substr($text, $prev_pos, $pos + strlen($search_text) - $prev_pos),
            ];
            $prev_pos = $pos + strlen($search_text);
        }

        foreach ($blocks as $block) {
            // exclude links to customer zone
            if (
                preg_match('/cart\.php/', $block['body'])
                || !preg_match('/(\?|&)action=/', $block['body'], $matches)
            ) {
                continue;
            }

            $action_text = $matches[0];
            $link_symbol = $matches[1];
            $pos = strpos($block['body'], $action_text);
            if ($pos !== false) {
                $genFormId = $this->getXliteFormIDText();
                $echo = $link_symbol . \XLite::FORM_ID . '=' . $genFormId . '&action=';
                $this->subst(
                    $text_start + $block['start'] + $pos,
                    $text_start + $block['start'] + $pos + strlen($action_text),
                    $echo
                );
            }
        }
    }

    // {{{ New code

    /**
     * Flag
     *
     * @var boolean
     */
    protected $checkTemplateStatus = true;

    /**
     * Root directory path length
     *
     * @var integer
     */
    protected $rootDirLength;

    /**
     * Compile and save template
     *
     * @param string  $original Relative file path
     * @param boolean $force    Flag to force compilation OPTIONAL
     *
     * @return string
     */
    public function prepare($original, $force = false)
    {
        $compiled = $this->compileDir . substr($original, $this->rootDirLength) . '.php';

        if (!$this->isTemplateValid($original, $compiled) || $force) {
            \Includes\Utils\FileManager::write($compiled, $this->parse($original));

            touch($compiled, filemtime($original));
        }

        return $compiled;
    }

    /**
     * Remove compiled template
     *
     * @param string $original Relative file path
     *
     * @return void
     */
    public function remove($original)
    {
        $compiled = $this->compileDir . substr($original, $this->rootDirLength) . '.php';

        \Includes\Utils\FileManager::deleteFile($compiled);
    }

    /**
     * Check if template is up-to-date
     *
     * @param string $original Original template
     * @param string $compiled Compiled one
     *
     * @return boolean
     */
    protected function isTemplateValid($original, $compiled)
    {
        return \Includes\Utils\FileManager::isExists($compiled)
            && (!$this->checkTemplateStatus || (filemtime($compiled) == filemtime($original)));
    }

    /**
     * Set new file for compile
     *
     * @param string $file Template to compile
     *
     * @return void
     */
    protected function init($file)
    {
        $this->file   = $file;
        $this->source = \Includes\Utils\FileManager::read($file);

        $this->urlRewrite = [
            'images' => [$this, 'rewriteImageURL'],
        ];
    }

    /**
     * Parse the "<widget />" tag
     *
     * @param array   $token    Token data
     * @param integer &$counter Counter
     *
     * @return void
     */
    protected function parseWidget(array $token, &$counter)
    {
        if (!strcasecmp($token['name'], 'widget')) {
            $attrs = $this->parseTagAttrs($counter);
            [$target, $module, $name] = $this->processWidgetAttrs($attrs);

            $this->displayTag($token, $this->widgetDisplayCode($attrs, $target, $module, $name));
        }
    }

    /**
     * Parse the "<list />" tag
     *
     * @param array   $token    Token data
     * @param integer &$counter Counter
     *
     * @return void
     */
    protected function parseList(array $token, &$counter)
    {
        if (!strcasecmp($token['name'], 'list')) {
            $this->displayTag($token, $this->getListDisplayCode($this->parseTagAttrs($counter)));
        }
    }

    /**
     * Parse arguments for "<widget ... />" and "<list ... />" tags
     *
     * @param integer &$counter Counter
     *
     * @return array
     */
    protected function parseTagAttrs(&$counter)
    {
        $attrs = [];

        while (++$counter < count($this->tokens)) {
            $current = $this->tokens[$counter];

            if ($current['type'] === 'attribute') {
                $attr = $current['name'];
                $attrs[$attr] = true;
            } elseif ($current['type'] === 'attribute-value') {
                $attrs[$attr] = $this->getTokenText($counter);
            } else {
                $counter--;
                break;
            }
        }

        return $attrs;
    }

    /**
     * Display "<widget ... />" and "<list ... />" tags
     *
     * @param array  $token Token data
     * @param string $code  Code to display
     *
     * @return void
     */
    protected function displayTag(array $token, $code)
    {
        if (!empty($code)) {
            $code = static::PHP_OPEN . ' ' . $code . ' ?';
            $token['end']--;
        }

        $this->subst($token['start'], $token['end'], $code);
    }

    /**
     * Process widget attributes
     *
     * @param array $attrs Attributes
     *
     * @return array
     */
    protected function processWidgetAttrs(array &$attrs)
    {
        $result = [];
        $names  = ['target', 'module', 'name'];

        foreach ($names as $index) {
            $result[] = \Includes\Utils\ArrayManager::getIndex($attrs, $index);
        }

        if (isset($attrs['if'])) {
            $attrs['IF'] = $attrs['if'];
        }

        $this->unsetAttributes($attrs, array_merge($names, ['if']));

        return $result;
    }

    /**
     * Return code for the parsed "<list ... />" tag
     *
     * @param array $attrs All tag attributes
     *
     * @return string
     */
    protected function getListDisplayCode(array $attrs)
    {
        $type = ucfirst(\Includes\Utils\ArrayManager::getIndex($attrs, 'type'));
        $name = \Includes\Utils\ArrayManager::getIndex($attrs, 'name');
        $this->unsetAttributes($attrs, ['type', 'name']);

        $args = '';
        if (!empty($attrs)) {
            $args .= ', ' . $this->getAttributesList($attrs);
        }

        return '$this->display' . $type . 'ViewListContent(' . $this->flexyAttribute($name) . $args . ');';
    }

    /**
     * Constructor
     *
     * @return void
     */
    protected function __construct()
    {
        parent::__construct();

        $this->checkTemplateStatus = LC_DEVELOPER_MODE
            || \Includes\Utils\ConfigParser::getOptions(['skin_details', 'check_templates_status']);

        $this->rootDirLength = strlen(LC_DIR_ROOT);

        $this->compileDir = LC_DIR_COMPILE;
    }
}
