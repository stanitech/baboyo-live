<?php
$routes->post('login', '\Accounts\Controllers\Accounts::login'); 
$routes->get('logout', '\Accounts\Controllers\Accounts::logout'); 


$routes->get('newsletter/(:any)/(:alpha)', '\Accounts\Controllers\Accounts::subscribeToNewsLetterViaURL/$1/$2'); 
$routes->post('subscribe-to-newsletter', '\Accounts\Controllers\Accounts::subscribeToNewsLetter'); 

$routes->group("account",function($routes){
    $routes->get('your-info', '\Accounts\Controllers\Accounts::yourInfo',['filter'=>"authorized_only"]); 

    $routes->get('prediction-studio', '\Accounts\Controllers\Accounts::predictionStudio',['filter'=>"authorized_only:EXPERT,SUPER USER"]);
    $routes->get('prediction-studio/(:any)', '\Accounts\Controllers\Accounts::predictionStudio/$1',['filter'=>"authorized_only:EXPERT,SUPER USER"]);

    $routes->post('send-message', '\Accounts\Controllers\Accounts::sendMessage',['filter'=>"authorized_only:ADMINISTRATOR,SUPER USER"]);
    
    $routes->post('send-invitation', '\Accounts\Controllers\Accounts::sendInvitation',['filter'=>"authorized_only:ADMINISTRATOR,SUPER USER"]);

    $routes->get('manage-subscription', '\Accounts\Controllers\Accounts::manageSubscription',['filter'=>"authorized_only:EXPERT,SUPER USER"]); 
    $routes->post('manage-subscription', '\Accounts\Controllers\Accounts::createPackage',['filter'=>"authorized_only:EXPERT,SUPER USER"]); 

    $routes->post('subscribe', '\Accounts\Controllers\Accounts::subscribe',['filter'=>"authorized_only"]); 

    $routes->get('subscribers', '\Accounts\Controllers\Accounts::subscribers',['filter'=>"authorized_only:SUPER USER"]); 

    $routes->get('transaction/(:alphanum)', '\Accounts\Controllers\Accounts::transaction/$1',['filter'=>"authorized_only"]);
    $routes->get('transactions', '\Accounts\Controllers\Accounts::transactions',['filter'=>"authorized_only:SUPER USER"]);



});


$routes->group("settings",function($routes){
    $routes->get('manage-accounts', '\Accounts\Controllers\Accounts::manageAccounts',['filter'=>"authorized_only:ADMINISTRATOR,SUPER USER"]); 

    $routes->get('manage-newsletter-subscribers', '\Accounts\Controllers\Accounts::manageNewsletterSubscribers',['filter'=>"authorized_only:ADMINISTRATOR,SUPER USER"]); 

    $routes->delete('manage-newsletter-subscribers', '\Accounts\Controllers\Accounts::deleteNewsletterSubscribers',['filter'=>"authorized_only:ADMINISTRATOR,SUPER USER"]); 

    $routes->post('manage-accounts', '\Accounts\Controllers\Accounts::saveAccount'); 
    $routes->post('manage-accounts/(:alpha)', '\Accounts\Controllers\Accounts::saveAccount/true'); 
    $routes->delete('manage-accounts', '\Accounts\Controllers\Accounts::deleteAccount',['filter'=>"authorized_only:ADMINISTRATOR,SUPER USER"]); 

    $routes->get('platform-updates', '\Accounts\Controllers\Accounts::platformUpdates',['filter'=>"authorized_only:ADMINISTRATOR,SUPER USER"]); 
    $routes->get('platform-updates/(:any)', '\Accounts\Controllers\Accounts::platformUpdates/$1',['filter'=>"authorized_only:ADMINISTRATOR,SUPER USER"]); 

    $routes->get('update-history', '\Accounts\Controllers\Accounts::updateHistory',['filter'=>"authorized_only:ADMINISTRATOR,SUPER USER"]);
    $routes->get('clear-history', '\Accounts\Controllers\Accounts::clearHistory',['filter'=>"authorized_only:ADMINISTRATOR,SUPER USER"]); 

    $routes->get('ad-management', '\Accounts\Controllers\Accounts::adManagement',['filter'=>"authorized_only:ADMINISTRATOR,SUPER USER"]);
}); 

