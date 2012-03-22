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

// This should attach this path but it doesn't???
# FIXME:
            'orm_driver_chain' => array(
                'parameters' => array(
                    'drivers' => array(
                        'attachments_annotation_driver' => array(
                            'class'     => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                            'namespace' => 'Entities',
                            'paths' => array(
                                APPLICATION_PATH . '/modules/plugins/Attachments/src/Attachments/Entity',
                            )
                        )
                    )
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
