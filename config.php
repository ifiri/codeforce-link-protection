<?php

return array(
    // Predefined default values, e.g. default parameters
    'defaults' => array(
        'templates' => [
            'admin' => [
                'pages' => [
                    'settings' => [
                        'name' => 'settings',
                        'path' => '{admin}/pages/',
                    ]
                ],
            ],

            'client' => [
                'default-template' => 'default',
                'widget' => [
                    'name' => 'widget',
                    'path' => '{client}/modules/',
                ],
            ],
        ],
    ),

    // Predefined system values, e.g. logical operators, needly constants or system paths
    'system' => array(
        'template-directories' => array(
            'plugin' => [
                'placeholder' => '{plugin}',
                'path' => \TeaLinkProtection\PLUGIN_PATH,
            ],

            'admin' => [
                'placeholder' => '{admin}',
                'path' => \TeaLinkProtection\PLUGIN_PATH . '/templates/admin',
            ],
            'client' => [
                'placeholder' => '{client}',
                'path' => \TeaLinkProtection\PLUGIN_PATH . '/templates/client',
            ],
            'layouts' => [
                'placeholder' => '{layouts}',
                'path' => \TeaLinkProtection\PLUGIN_PATH . '/templates/client/layouts',
            ],

            'theme' => [
                'placeholder' => '{theme}',
                'path' => get_stylesheet_directory() . '/templates',
            ],
        ),

        'versions' => array(
            'plugin' => '1.0',
            'scripts' => '1',
            'styles' => '1',
        ),

        'settings' => array(
        ),

        'notices' => array(
            'test' => 'Хочу лизать девушке ноги',
            'test2' => 'Хочу быть рабом девушки',

            'link-add-success' => __('Link was created successfully', 'tea-link-protection'),
            'link-update-success' => __('Link was updated successfully', 'tea-link-protection'),
            'link-delete-success' => __('Link was deleted successfully', 'tea-link-protection'),

            'link-add-fail' => __('During the link creation errors occurred. Link wasn\'t created.', 'tea-link-protection'),
            'link-update-fail' => __('During the link update errors occurred. Link wasn\'t updated.', 'tea-link-protection'),
            'link-delete-fail' => __('During the link deletion errors occurred. Link wasn\'t deleted.', 'tea-link-protection'),

            'links-bulk-delete-success' => __('All links was deleted successfully', 'tea-link-protection'),
            'links-bulk-delete-fail' => __('During the links deletion errors occured. Links wasn\'t deleted.', 'tea-link-protection'),

            'out-of-date-nonce' => __('This url is out of date or was compromised. Try again.', 'tea-link-protection'),
            'incorrect-request' => __('Current request is broken. Try again.', 'tea-link-protection'),

            'something-wrong-with-action' => __('In processing of your request errors occured. Please, try again.'),
        ),

        'pages' => [
            'LinksList' => [
                'order' => 1,

                // alias => path\to\element
                'elements' => [
                    'LinksListTable' => 'LinksListTable',
                ],
                'screen-options' => [
                    'per_page' => [
                        'label' => __('Links Per Page', 'tea-link-protection'),
                        'default' => 20,
                        'option' => 'links_per_page'
                    ],
                ],
            ],
            'CreateNewLink' => [
                'order' => 2,
            ],
            'EditLink' => [
                'order' => 3,
            ],
        ],
    ),

    // todo в конце, если никаких параметров не появится, просто сделать перечисление
    'link' => [
        'types' => [
            'common' => [
            ],
            'attachment' => [
            ],
        ],

        'rules' => [
            'IP_RESTRICT' => [
            ],
            'ROLE_RESTRICT' => [
            ],
            'DISPOSABLE' => [
            ],
        ],

        'aliases' => [
            'params' => [
                'IP_ADDRESS',
                'ROLE',
                'CAPABILITY',
                'CLICKS',
                'LIFETIME',
            ],
        ],
    ],

    'supported-actions' => [
        'create-new-link',
        'update-link',
        'delete',

        'bulk-delete',
    ],
);