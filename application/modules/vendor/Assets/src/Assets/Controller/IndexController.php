<?php

namespace Assets\Controller;

use ReflectionClass,
    CssMin,
    RecursiveIteratorIterator,
    RecursiveDirectoryIterator,
    FilesystemIterator as FsIterator,
    Zend\Mvc\Controller\ActionController;

class IndexController extends ActionController
{
    const DATE_FORMAT = 'D, d M Y H:i:s \G\M\T';
    const MAX_AGE = 2592000; // 30 * 24 * 60 * 60
    protected $types = array(
        'css'  => 'text/css',
        'js'   => 'text/javascript',
        'html' => 'text/html',
        'htm'  => 'text/htm',
        'txt'  => 'text/plain',
        'png'  => 'image/png',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif'  => 'image/gif',
        'bmp'  => 'image/bmp',
        'json' => 'application/json',
        'xml'  => 'text/xml',
    );

    public function indexAction()
    {
        $req = $this->request;
        $res = $this->response;

        $route  = $this->event->getRouteMatch();
        $module = $route->getParam('module');
        $asset  = $route->getParam('asset');

        $cont = null;
        $lastMod = $req->server()->get('HTTP_IF_MODIFIED_SINCE');


        $parts = explode('/', $asset, 2);
        if (count($parts) == 2) {
            list($type, $rest) = $parts;
            $method = 'process' . $type;

            if (method_exists($this, $method)) {
                $cont = $this->$method($module, $rest, $lastMod);
            }
        }

        if (null === $cont && ($file = $this->pathTo($module, $asset))) {


            $ext = pathinfo($file, PATHINFO_EXTENSION);
            $modTime = filemtime($file);

            $age = self::MAX_AGE;
            $res->headers()
                ->addHeaders(array(
                    'Content-Type' => $this->getType($ext),
                    'Cache-Control' => 'public, max-age=' . $age,
                    'Last-Modified' => gmdate(self::DATE_FORMAT, $modTime),
                    'Expires' => gmdate(self::DATE_FORMAT, time() + $age),
                    'Pragma' => '',
                ));
            if (empty($lastMod) || $modTime > strtotime($lastMod)) {
                $cont = file_get_contents($file);
            } else {
                $res->setStatusCode(304);
                return $res;
            }
        }

        if (null === $cont) {
            $res->setStatusCode(404);
        } else {
            $res->setContent($cont);
            // TODO: commented until i figure out a good invalidation strategy
            //$this->cache($module . '/' . $asset, $cont);
        }

        return $res;
    }

    protected function processCss($module, $asset, $lastMod)
    {
        $res = $this->response;

        if (in_array($asset, array('all.css', 'debug.css'))
            && ($dir = $this->pathTo($module, 'css'))) {
            $css = '';
            $modTime = time();
            $fnOffset = strlen(dirname($dir));
            $dir = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($dir, FsIterator::SKIP_DOTS));
            foreach ($dir as $fn => $file) {
                if ('css' == pathinfo($fn, PATHINFO_EXTENSION)) {
                    $shortfn = substr($fn, $fnOffset);
                    $css .= "/* ...$shortfn */\n" . file_get_contents($fn) . "\n";
                }
            }
        }

        if (empty($css)) {
            $file = $this->pathTo($module, 'css/' . $asset);
            if (!$file) {
                return;
            }
            $modTime = filemtime($file);
            if (empty($lastMod) || $modTime > strtotime($lastMod)) {
                $css = file_get_contents($file);
            } else {
                $css = '';
                $res->setStatusCode(304);
            }
        }

        if (!empty($css) && 'debug.css' !== $asset) {
            require_once __DIR__ . '/../../cssmin/CssMin.php';
            $css = CssMin::minify($css);
        }

        $format = self::DATE_FORMAT;
        $age = self::MAX_AGE;
        $res->headers()
            ->addHeaders(array(
                'Content-Type' => 'text/css',
                'Last-Modified' => gmdate($format, $modTime),
                'Expires' => gmdate($format, time() + $age),
            ));

        return $css;
    }

    protected function cache($filename, $contents)
    {
        $assetDir = getcwd() . '/assets';
        if (is_writeable($assetDir)) {
            $file = $assetDir . '/' . $filename;
            $dir = dirname($file);
            if (!file_exists($dir)) {
                mkdir(dirname($file), 0755, true);
            }
            file_put_contents($file, $contents);
        }
    }

    protected function pathTo($module, $asset)
    {
        $clsName = $module . '\Module';
        $ret = false;
        if (class_exists($clsName, false)) {
            $cls = new ReflectionClass($clsName);
            $dir = dirname($cls->getFilename());
            $path = array($dir, 'public', $asset);
            $ret = realpath(implode(DIRECTORY_SEPARATOR, $path));
        }
        return $ret;
    }

    protected function getType($ext, $def = 'text/plain')
    {
        return isset($this->types[$ext]) ? $this->types[$ext] : $def;
    }
}
