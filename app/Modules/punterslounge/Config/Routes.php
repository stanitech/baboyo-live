<?php

$routes->get('/', '\PuntersLounge\Controllers\PuntersLounge::index'); 
$routes->get('/livescore/(:any)', '\PuntersLounge\Controllers\PuntersLounge::index/$1'); 
$routes->post('/livescore', '\PuntersLounge\Controllers\PuntersLounge::resyncLivescore',['filter'=>"authorized_only:ADMINISTRATOR,SUPER USER"]);
$routes->get('/tips', '\PuntersLounge\Controllers\PuntersLounge::tips');
$routes->get('/teams', '\PuntersLounge\Controllers\PuntersLounge::teams');
$routes->get('/team-single', '\PuntersLounge\Controllers\PuntersLounge::team');    

$routes->get('/news/(:any)', '\PuntersLounge\Controllers\PuntersLounge::posts/$1'); 
$routes->get('/post/(:any)', '\PuntersLounge\Controllers\PuntersLounge::post/$1'); 

$routes->get('/get-predictions-tip', '\PuntersLounge\Controllers\PuntersLounge::getPredictionsTip',['filter'=>"authorized_only:EXPERT,ADMINISTRATOR,SUPER USER"]); 
$routes->get('/get-predictions-tip/(:any)', '\PuntersLounge\Controllers\PuntersLounge::getPredictionsTip/$1',['filter'=>"authorized_only:EXPERT,ADMINISTRATOR,SUPER USER"]); 
$routes->post('/get-predictions-tip', '\PuntersLounge\Controllers\PuntersLounge::resyncPredictions',['filter'=>"authorized_only:ADMINISTRATOR,SUPER USER"]); 

$routes->get('/predictions', '\PuntersLounge\Controllers\PuntersLounge::getPredictions',['filter'=>"authorized_only:EXPERT,ADMINISTRATOR,SUPER USER"]);
$routes->get('/predictions/(:any)', '\PuntersLounge\Controllers\PuntersLounge::getPredictions/$1',['filter'=>"authorized_only:EXPERT,ADMINISTRATOR,SUPER USER"]);

$routes->get('/predictions-statistics', '\PuntersLounge\Controllers\PuntersLounge::predictionsStatistics');
$routes->get('/predictions-statistics/(:segment)', '\PuntersLounge\Controllers\PuntersLounge::predictionsStatistics/$1');


$routes->post('/set-prediction', '\PuntersLounge\Controllers\PuntersLounge::setPrediction',['filter'=>"authorized_only:EXPERT,ADMINISTRATOR,SUPER USER"]);

$routes->get('/experts', '\PuntersLounge\Controllers\PuntersLounge::experts'); 


$routes->get('/expert/prediction/(:any)/(:any)', '\PuntersLounge\Controllers\PuntersLounge::expertPrediction/$1/$2');

$routes->group("settings",['filter'=>"authorized_only:SUPER USER,CONTENT WRITER"],function($routes){
    $routes->get('manage-posts', '\PuntersLounge\Controllers\PuntersLounge::managePosts');
    $routes->post('manage-posts', '\PuntersLounge\Controllers\PuntersLounge::savePost');
    $routes->get('manage-posts/(:alpha)', '\PuntersLounge\Controllers\PuntersLounge::managePosts/$1');
    $routes->get('manage-posts/(:alpha)/(:any)', '\PuntersLounge\Controllers\PuntersLounge::managePosts/$1/$2');
    $routes->post('manage-posts-batch-operations', '\PuntersLounge\Controllers\PuntersLounge::batchArticlesOperations');

    $routes->get('manage-post-category', '\PuntersLounge\Controllers\PuntersLounge::postCategories');
    $routes->post('manage-post-category', '\PuntersLounge\Controllers\PuntersLounge::savePostCategory');
    $routes->get('manage-post-category/(:alpha)', '\PuntersLounge\Controllers\PuntersLounge::postCategories/$1');
    $routes->get('manage-post-category/(:alpha)/(:any)', '\PuntersLounge\Controllers\PuntersLounge::postCategories/$1/$2');
    $routes->post('manage-posts-category-batch-operations', '\PuntersLounge\Controllers\PuntersLounge::batchPostCategoryOperations');


});

