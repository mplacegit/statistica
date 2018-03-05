<?php 
Route::group(['prefix' => 'mpstatistica'
, 'as' => 'mpstatistica.'
,'middleware' => ['web', 'auth']
,'namespace'=>'Mplacegit\Statistica\Controllers']
,function (){
      Route::get('loaded/{id}', ['as' => 'loaded_server','uses'=>'LoadedController@server']);
      Route::get('loaded', ['as' => 'loaded','uses'=>'LoadedController@index']);
});
