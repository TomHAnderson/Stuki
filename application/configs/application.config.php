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
            APPLICATION_PATH . '/modules/core',      # core Stuki modules
            APPLICATION_PATH . '/modules/vendor',       # 3rd party modules
            APPLICATION_PATH . '/modules/renderers',    # Stuki renderers
            APPLICATION_PATH . '/modules/plugins',      # entity plugins
            APPLICATION_PATH . '/modules/client',       # client specific modules
        ),
    ),

    'modules' => array(
        // Vendors - load first
        'DoctrineModule',
        'DoctrineORMModule',
        'Assets',

        // Stuki core
        'Stuki',
        'StuQL',

        // Additional Core Stuki Modules
        'Install',  # Comment out after installation
        'Search',

        // Renderers
        'DefaultRenderers',
        'Html',
        'GoogleDocs',

        // Entity Plugins
        'Attachments',
        'Favorites',

        // Client Specific
        # e.g. overriding default layout
        'DefaultLayout',

        // Vendors - load last
        'ZfcBase',
        'ZfcUser',
        'ZfcUserDoctrineORM',
    ),
);
