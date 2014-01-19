<?php
// This is the main Web application configuration. Any writable
// application properties can be configured here.
return array(
    'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
    'name'=>'Learzing',

    // autoloading model and component classes
    'import'=>array(
        'application.models.*',
        'application.models.api.*',
        'application.models.api.v1.*',
        'application.models.auth.*',
        'application.models.auth.hybrid_auth.*',
        'application.models.auth.hybrid_auth.postgres.*',
        'application.models.entities.*',
        'application.services.*',
        'application.services.postgres.*',
        'application.services.auth.*',
        'application.services.auth.hybrid_auth.*',
        'application.services.auth.hybrid_auth.postgres.*',
        'application.utils.*',
        'application.components.*',
        'application.controllers.*',
        'application.controllers.api.*',
        'application.controllers.api.v1.*',
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
            'rules'=>array(
                // REST patterns
                //auth services
                array('api/v1/ApiAuth/GetAccessToken', 'pattern' => 'auth/token', 'verb' => 'GET'),
                array('api/v1/ApiAuth/DestroyAccessToken', 'pattern' => 'auth/token', 'verb' => 'DELETE'),
                //user services
                array('api/v1/ApiUser/GetUser', 'pattern' => 'api/user', 'verb' => 'GET'),
                array('api/v1/ApiUser/ModifyUser', 'pattern' => 'api/user', 'verb' => 'PUT'),
                array('api/v1/ApiUser/RegisterUser', 'pattern' => 'api/user', 'verb' => 'POST'),
                //friends service
                array('api/v1/ApiFriends/GetFriends', 'pattern' => 'api/friends', 'verb' => 'GET'),
                array('api/v1/ApiFriends/AddFriend', 'pattern' => 'api/friends', 'verb' => 'POST'),
                array('api/v1/ApiFriends/DeleteFriend', 'pattern' => 'api/friends', 'verb' => 'DELETE'),
                //messaging service
                array('api/v1/ApiMessaging/GetMessages', 'pattern' => 'api/messaging', 'verb' => 'GET'),
                array('api/v1/ApiMessaging/SendMessage', 'pattern' => 'api/messaging', 'verb' => 'POST'),
                //search service
                array('api/v1/ApiSearch/Search', 'pattern' => 'api/search', 'verb' => 'GET'),
                //skills service
                array('api/v1/ApiSkills/GetUserSkills', 'pattern' => 'api/skills', 'verb' => 'GET'),
                array('api/v1/ApiSkills/ModifyCurrentUserSkill', 'pattern' => 'api/skills', 'verb' => 'PUT'),
                //Rules for web pages
                array('user/ShowUserProfile', 'pattern' => 'user/profile/<userid:.*>', 'verb' => 'GET'),
                array('user/ShowUserProfile', 'pattern' => 'user/profile', 'verb' => 'GET'),
                // Default rule
                '<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
            ),
        ),
        'user' => array(
            'returnUrl' => '/'
        )
    ),
    /*
     * We don't use hybrid auth module now. But don't delete this info.
     * We'll need it implement our own hybrid auth.
     */
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
                    "enabled" => false,
                    "keys"    => array ( "key" => "LwiRbzXDGhzvhuvPFLRkg", "secret" => "LMcDHie2pNJ9N8xNa4EfNaIqHKqDB27R5sOylu8Ci8w" ),
                    "showOnSignup" => false,
                ),
                //Don't show on signup - doesn't provide email. Need request it from user - not implemented
                "Vkontakte" => array ( 
                    "enabled" => false,
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