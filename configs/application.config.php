<?php
/**
 * Stuki
 *
 * This configures the application before module ini files are merged
 */

return array(
    'module_listener_options' => array(
        'config_cache_enabled' => false,
        'cache_dir' => 'data/Cache',
        'module_paths' => array(
            __DIR__ . '/../modules/core',      # core Stuki modules
            __DIR__ . '/../vendor',       # 3rd party modules
            __DIR__ . '/../modules/renderers',    # Stuki renderers
            __DIR__ . '/../modules/plugins',      # entity plugins
            __DIR__ . '/../modules/client',       # client specific modules
        ),
    ),

    'modules' => array(
        // Vendors - load first
        'DoctrineModule',
        'DoctrineORMModule',
        'AssetsCompiler',

        // Stuki core
        'Stuki',
        'StuQL',

        // Additional Core Stuki Modules
        'Install',  # Comment out after installation
        'Search',
#        'Auditing',

        // Renderers
        'DefaultRenderers',
        'Html',
        'GoogleDocs',

        // Entity Plugins
        'Attachments',
        'Favorites',
#        'FacebookComments',

        // Client Specific
        'DefaultLayout',

        // Vendors - load last
#        'ZfcBase',
#        'ZfcUser',
#        'ZfcUserDoctrineORM',
    ),
);
