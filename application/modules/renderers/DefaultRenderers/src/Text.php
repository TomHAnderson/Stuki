<?php

namespace DefaultRenderers;

use Stuki\Renderer\Renderer,
    Zend\Form\Element\Textarea;

class Text extends Textarea implements Renderer
{
    private $datatype = 'text';

    public function getDataType() {
        return $this->datatype;
    }

    public function postCreate() {
        $this->rows = 3;
        $this->cols = 58;

        $this->class = "resize";
    }

    public function formatValue($value) {
        // Replace http:// urls with links
        $value = nl2br(htmlentities($value, ENT_COMPAT, 'UTF-8'));
        $value = $this->clickable_link($value);
        $value = str_replace('  ', '&nbsp;&nbsp;', $value);
        return $value;
    }

    // Preformat value when editing such as date times > dates
    public function formatEditValue($value) {
        return $value;
    }

    // Format for search
    public function formatSearchValue($value) {
        return $value;
    }

    /**
     * Return an about section of the ini
     */
    public function getAbout($field) {
        $ini = new \Zend_Config_Ini(__DIR__ . '/plugin.ini', 'about');
        return $ini->$field;
    }

    function clickable_link($text)
    {
        # this functions deserves credit to the fine folks at phpbb.com

        $text = preg_replace('#(script|about|applet|activex|chrome):#is', "\\1:", $text);

        // pad it with a space so we can match things at the start of the 1st line.
        $ret = ' ' . $text;

        // matches an "xxxx://yyyy" URL at the start of a line, or after a space.
        // xxxx can only be alpha characters.
        // yyyy is anything up to the first space, newline, comma, double quote or <
        $ret = preg_replace("#(^|[\n ])([\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]*)#is", "\\1<a href=\"\\2\" target=\"_blank\">\\2</a>", $ret);

        // matches a "www|ftp.xxxx.yyyy[/zzzz]" kinda lazy URL thing
        // Must contain at least 2 dots. xxxx contains either alphanum, or "-"
        // zzzz is optional.. will contain everything up to the first space, newline,
        // comma, double quote or <.
        $ret = preg_replace("#(^|[\n ])((www|ftp)\.[\w\#$%&~/.\-;:=,?@\[\]+]*)#is", "\\1<a href=\"http://\\2\" target=\"_blank\">\\2</a>", $ret);

        // matches an email@domain type address at the start of a line, or after a space.
        // Note: Only the followed chars are valid; alphanums, "-", "_" and or ".".
        $ret = preg_replace("#(^|[\n ])([a-z0-9&\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)#i", "\\1<a href=\"mailto:\\2@\\3\">\\2@\\3</a>", $ret);

        // Remove our padding..
        $ret = substr($ret, 1);
        return $ret;
    }
}
