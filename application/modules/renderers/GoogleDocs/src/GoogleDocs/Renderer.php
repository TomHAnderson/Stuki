<?php

namespace GoogleDocs;

use Stuki\Renderer\Renderer as StukiRenderer,
    Zend\Form\Element\Text,
    Zend\Gdata\ClientLogin,
    Zend\Gdata\Docs;

class Renderer extends Text implements StukiRenderer
{
    private $datatype = 'varchar';

    public function getDataType() {
        return $this->datatype;
    }

    public function postCreate() {
        $this->size = 55;

        $this->class = "resize";
    }

    public function formatValue($value) {
        $module = new \GoogleDocs\Module;
        $config = $module->getConfig();

        $user = $config['gdocs']['username'];
        $pass = $config['gdocs']['password'];
        $docId = $value;

        $client = ClientLogin::getHttpClient($user, $pass, \Zend\Gdata\Docs::AUTH_SERVICE_NAME);
        $docs = new Docs($client, 'Stuki ' . \Stuki\Version::VERSION);

        $doc = $docs->getDocument($docId);
        $res = $docs->performHttpRequest('GET', $doc->getContent()->getSrc());

        foreach ($doc->getLink() as $url) {
            if ($url->getRel() == 'alternate')
                $return = $doc->getTitle() . ' <a target="_blank" href="' . $url->getHref() . '">Edit</a> ';
        }

        $return .= ' <a href="#" onClick="window.location.reload();return false;">Reload</a>';
        $return .= ' <br><iframe style="width: 600px; height: 250px;" src="http://apple.stuki.localhost/googledocs/view?docid=' . $docId . '"></iframe>';


        return $return;
    }

    // Preformat value when editing such as date times > dates
    public function formatEditValue($value) {
        return $value;
    }

    // Format for search
    public function formatSearchValue($value) {
        $module = new \GoogleDocs\Module;
        $config = $module->getConfig();

        $user = $config['gdocs']['username'];
        $pass = $config['gdocs']['password'];
        $docId = $value;

        $client = ClientLogin::getHttpClient($user, $pass, \Zend\Gdata\Docs::AUTH_SERVICE_NAME);
        $docs = new Docs($client, 'Stuki ' . \Stuki\Version::VERSION);

        $doc = $docs->getDocument($docId);
        $res = $docs->performHttpRequest('GET', $doc->getContent()->getSrc());

        # FIXME: this simple strip tags should be replaced with a proper parser
        return (strip_tags($res->getBody()));
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
