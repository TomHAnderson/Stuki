<?php

namespace GoogleDocs\Controller;

use Zend\Mvc\Controller\ActionController,
    Zend\Gdata\ClientLogin,
    Zend\Gdata\Docs;

class IndexController extends ActionController
{
    public function viewAction()
    {
        if (!$docId = $this->getRequest()->query()->get('docid'))
            throw new \Stuki\Exception('No doc id given');

        $module = new \GoogleDocs\Module;
        $config = $module->getConfig();

        $user = $config['gdocs']['username'];
        $pass = $config['gdocs']['password'];

        $client = ClientLogin::getHttpClient($user, $pass, \Zend\Gdata\Docs::AUTH_SERVICE_NAME);
        $docs = new Docs($client, 'Stuki ' . \Stuki\Version::VERSION);

        $doc = $docs->getDocument($docId);
        $res = $docs->performHttpRequest('GET', $doc->getContent()->getSrc());

        foreach ($doc->getLink() as $url) {
            if ($url->getRel() == 'alternate')
                $return = $doc->getTitle() . ' <a target="_blank" href="' . $url->getHref() . '">Edit</a><br>';
        }

        // Disable layout
        $this->getLocator()->get('view')->layout()->disableLayout();

        return array(
            'document' => $res->getBody()
        );
    }
}
