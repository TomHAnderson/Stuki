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
        // Core
        'Install',  # Comment out after installation if desired
        'Stuki',
        'StuQL',

        // Additional Core Stuki Modules
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
        'StukiLayout',

        // Vendors - Soliant
        'SoliantAssetsCompiler',
        'SoliantDoctrineQueue',

        // Vendors - load last
        'DoctrineModule',
        'DoctrineORMModule',
        'ZfcBase',
        'ZfcUser',
        'ZfcUserDoctrineORM',
    ),
);
