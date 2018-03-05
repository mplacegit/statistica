<?php 
Route::group(['prefix' => 'mpstatistica'
, 'as' => 'mpstatistica.'
,'middleware' => ['role:admin|manager|super_manager']
,'namespace'=>'Mplacegit\Statistica\Controllers']
,function (){
      Route::get('loaded', ['as' => 'loaded','uses'=>'LoadedController@index']);
   
});
