<?php
// This is the main Web application configuration. Any writable
// application properties can be configured here.
return array(
    'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
    'name'=>'Learzing',

    // autoloading model and component classes
    'import'=>array(
        'application.models.*',
        'application.models.postgres.*',
        'application.models.hybrid_auth.*',
        'application.components.*',
        'application.controllers.*',
        'application.modules.hybridauth.*',
        'application.modules.hybridauth.controllers.*'
    ),

    // application components
    'components'=>array(
        /*'db'=>array(
            'connectionString'=>'pgsql:host=localhost;port=5432;dbname=postgres',
            'username'=>'postgres',
            'password'=>'111',
            'tablePrefix'=>'egp.',
        ),*/
        'urlManager'=>array(
                'showScriptName' => false,
                'urlFormat'=>'path',
        ),
        'user' => array(
            'returnUrl' => '/'
        )
    ),

    'modules'=> array(
        'hybridauth' => array(
            'baseUrl' => 'http://'. $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . '/hybridauth',
            //"baseUrl" => "http://phrenlabs.co.uk/VBM/hybridauth/",
            'withYiiUser' => false, // Set to true if using yii-user
            "providers" => array (
                "Google" => array ( 
                    "enabled" => true,
                    "keys"    => array ( "id" => "982565677299", "secret" => "XQgmQtMC3DnsJnN5wRoWXiVj" ),
                    "showOnSignup" => true,
                ),

                "Facebook" => array ( 
                    "enabled" => true,
                    "keys"    => array ( "id" => "217539541750355", "secret" => "ee9ae5708170c6a8f30b1404d58f5653" ),
                    "scope"   => "email, user_about_me",
                    "display" => "popup",
                    "showOnSignup" => true,
                ),
                //Don't show on signup - doesn't provide email. Need request it from user - not implemented
                "Twitter" => array ( 
                    "enabled" => true,
                    "keys"    => array ( "key" => "LwiRbzXDGhzvhuvPFLRkg", "secret" => "LMcDHie2pNJ9N8xNa4EfNaIqHKqDB27R5sOylu8Ci8w" ),
                    "showOnSignup" => false,
                ),
                //Don't show on signup - doesn't provide email. Need request it from user - not implemented
                "Vkontakte" => array ( 
                    "enabled" => true,
                    "keys"    => array ( "id" => "3986656", "secret" => "MywzE8lVxd861Uu6rm2I" ),
                    "scope"        => "offline, wall, friends, email",
                    "showOnSignup" => false,
                ),
                //disable as not popular
                "Yahoo" => array ( 
                    "enabled" => false,
                    "keys"    => array (
                        "id" => "dj0yJmk9QWFBV3kyY3FCelNNJmQ9WVdrOVltWjJaakprTm0wbWNHbzlPVGM1TnpneE16WXkmcz1jb25zdW1lcnNlY3JldCZ4PTIx",
                        "secret" => "4339f6d779c1172c2ad35c0d79d4ecc8b3a82470"),
                    "showOnSignup" => false,
                ),
        )),
    ),
);