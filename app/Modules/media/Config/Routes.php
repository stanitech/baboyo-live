<?php
$routes->group('/administrator',["namespace"=>'Media\Controllers'], function($routes){
    $routes->group('media', function($routes){
        $routes->get('', 'Media::index');
        $routes->get('get-media-ajax/(:num)', 'Media::getMediaAjax/$1');
        $routes->post('', 'Media::changeFile'); 
        $routes->post('(:alpha)', 'Media::apiHandleFileUpload'); 
        $routes->put('(:alpha)', 'Media::updateFile/$1'); 
        $routes->put('', 'Media::updateFile');
        $routes->delete('', 'Media::deleteFile');
        $routes->delete('api', 'Media::apiDeleteFile');
    });
});
