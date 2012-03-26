<?php

return array(
    'di' => array(
        'instance' => array(
            'alias' => array(
                // Controllers
                'attachments' => 'Attachments\Controller\IndexController',

                // Models
                'modelAttachments' => 'Attachments\Model\Attachments',
                'modelAttachmentsSearchIndex' => 'Attachments\Model\SearchIndex',
            ),

            'orm_driver_chain' => array(
                'parameters' => array(
                    'drivers' => array(
                        'attachments_xml_driver' => array(
                            'class'          => 'Doctrine\ORM\Mapping\Driver\XmlDriver',
                            'namespace'      => 'Attachments\Entity',
                            'paths'          => array(__DIR__ . '/xml'),
                            'file_extension' => '.dcm.xml',
                        ),
                    ),
                )
            ),

            'Zend\View\Resolver\TemplatePathStack' => array(
                'parameters' => array(
                    'paths' => array(
                        'attachments' => __DIR__ . '/../views',
                    ),
                ),
            ),
        ),
    ),
);
