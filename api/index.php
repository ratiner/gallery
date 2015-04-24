<?php

require_once('engine/utils.php');

$service = new Service();
$app = $service->app;



//search
$app->post('/search', function() use ($service) {
    $service->process('listing', 'search', $service->data);
});

//files
$app->get('/list/?:path+', function($path) use ($service) {
	$service->process('listing', '_list', str_replace('//', '/', implode('/', $path)));
});

//thumb
$app->get('/thumb/:width/:path+', function($width, $path) use ($service) {
    $service->process('thumb', 'Create', $width, str_replace('//', '/', implode('/', $path)));
});


$app->post('/file/:path+', function($path) use ($service) {
    $service->process('file', 'update', str_replace('//', '/', implode('/', $path)), $service->data);
});


//tags
$app->get('/tag/search/:text+', function($text) use ($service) {
    $service->process('tag', 'searchTags', implode('/', $text));
});


$app->post('/tag/one/:path+', function($path) use ($service) {
    $service->process('tag', 'saveOne', str_replace('//', '/', implode('/', $path)), $service->data);
});

$app->get('/tag/one/:path+', function($path) use ($service) {
    $service->process('tag', 'getTagsByPath', str_replace('//', '/', implode('/', $path)));
});

//hack
$app->options('/:any+', function($any) use ($service) {
    $service->OptionsFix();
});
$app->run();