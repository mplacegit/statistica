<?php 
Route::group(['prefix' => 'mpstatistica'
, 'as' => 'mpstatistica.'
,'middleware' => ['web', 'auth']
,'namespace'=>'Mplacegit\Statistica\Controllers']
,function (){
	  Route::get('partner_product/{id}', ['as' => 'partner_product','uses'=>'ProductController@partner']);
	  Route::get('partners_product', ['as' => 'partners_product','uses'=>'ProductController@partners']);
	  Route::get('pads_product', ['as' => 'pads_product','uses'=>'ProductController@pads']);
	  Route::get('summa_product', ['as' => 'summa_product','uses'=>'ProductController@index']);
      Route::get('loaded/{id}', ['as' => 'loaded_server','uses'=>'LoadedController@server']);
      Route::get('loaded', ['as' => 'loaded','uses'=>'LoadedController@index']);
});
