<?php

return array(
    'di' => array(
        'instance' => array(
            'alias' => array(
                // Controllers
#                'attachments' => 'Attachments\Controller\IndexController',

                // Models
#                'modelAttachments' => 'Attachments\Model\Attachments',
#                'modelAttachmentsSearchIndex' => 'Attachments\Model\SearchIndex',
            ),

            'Zend\View\Resolver\TemplatePathStack' => array(
                'parameters' => array(
                    'paths' => array(
                        'many2many' => __DIR__ . '/../view',
                    ),
                ),
            ),
        ),
    ),
);
