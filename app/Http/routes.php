<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
Route::get('/', function () {
    return view('welcome');
});

Route::get('/notfound', ['as' => 'notfound',
function () {
    return view('errors.404');
}]);

Route::get('/notauthorized', ['as' => 'notauthorized',
function () {
    return view('errors.401');
}]);

Route::group(['middleware' => ['auth']], function() {

//****************************************/ Start/*************************

	Route::resource('start','SSYS\SStartController');
	Route::get('/start',[
		'as' => 'start',
		'uses' => 'SSYS\SStartController@index'
	]);
	Route::post('/start/in',[
		'as' => 'start.getIn',
		'uses' => 'SSYS\SStartController@GetIn'
	]);

//****************************************/ Admin/*************************
	Route::group(['middleware' => ['mdadmin']], function() {

		Route::get('/admin',[
			'as' => 'plantilla.admin',
			'uses' => 'SPlantillaController@index'
		]);

  Route::group(['prefix' => 'admin'], function () {
  		Route::resource('users','SUsersController');
  		Route::get('users/{id}/activate', [
  			'uses' => 'SUsersController@Activate',
  			'as' => 'admin.users.activate'
  		]);
  		Route::get('users/{id}/copy', [
  			'uses' => 'SUsersController@Copy',
  			'as' => 'admin.users.copy'
  		]);
  		Route::get('users/{id}/destroy',[
  			'uses' => 'SUsersController@Destroy',
  			'as' => 'admin.users.destroy'
  		]);
  		Route::get('users/{id}/changepass',[
  			'uses' => 'SUsersController@ChangePass',
  			'as' => 'admin.users.changepass'
  		]);
  		Route::put('users/{id}/updatepass',[
  			'uses' => 'SUsersController@UpdatePass',
  			'as' => 'admin.users.updatepass'
  		]);

  		Route::resource('privileges','SSYS\SPrivilegesController');
  		Route::get('privileges/{id}/activate', [
  			'uses' => 'SSYS\SPrivilegesController@Activate',
  			'as' => 'admin.privileges.activate'
  		]);
  		Route::get('privileges/{id}/destroy',[
  			'uses' => 'SSYS\SPrivilegesController@Destroy',
  			'as' => 'admin.privileges.destroy'
  		]);

  		Route::resource('permissions','SSYS\SPermissionsController');
  		Route::get('permissions/{id}/activate', [
  			'uses' => 'SSYS\SPermissionsController@Activate',
  			'as' => 'admin.permissions.activate'
  		]);
  		Route::get('permissions/{id}/destroy',[
  			'uses' => 'SSYS\SPermissionsController@Destroy',
  			'as' => 'admin.permissions.destroy'
  		]);

  		Route::resource('userPermissions','SSYS\SUserPermissionsController');
  		Route::get('userPermissions/{id}/activate', [
  			'uses' => 'SSYS\SUserPermissionsController@Activate',
  			'as' => 'admin.userPermissions.activate'
  		]);
  		Route::get('userPermissions/{id}/destroy',[
  			'uses' => 'SSYS\SUserPermissionsController@Destroy',
  			'as' => 'admin.userPermissions.destroy'
  		]);

      Route::resource('companies','SSYS\SCompaniesController');
      Route::get('companies/{id}/destroy',[
  			'uses' => 'SSYS\SCompaniesController@Destroy',
  			'as' => 'admin.companies.destroy'
  		]);
      Route::get('companies/{id}/activate', [
  			'uses' => 'SSYS\SCompaniesController@Activate',
  			'as' => 'admin.companies.activate'
  		]);

      Route::resource('usraccess','SSYS\SUserCompaniesController');
    });

	});

//****************************************/ Company /*************************

	Route::group(['middleware' => ['mdcompany']], function() {

		Route::get('/modules',[
			'as' => 'start.selmod',
			'uses' => 'SSYS\SStartController@SelectModule'
		]);

//****************************************/ Manufacturing /*************************
		Route::get('/mms/home',[
			'as' => 'mms.home',
			'uses' => 'SMMS\SProductionController@Home'
		]);
		Route::resource('mms','SMMS\SProductionController');

//****************************************/ Quality Module /*************************
		Route::get('/qms/home',[
			'as' => 'qms.home',
			'uses' => 'SQMS\SQualityController@Home'
		]);
		Route::resource('qms','SQMS\SQualityController');

//****************************************/ Warehouses Module/*************************

  Route::group(['prefix' => 'wms'], function () {
  		Route::get('/home',[
  			'as' => 'wms.home',
  			'uses' => 'SWMS\SWmsController@Home'
  		]);
  		Route::resource('wms','SWMS\SWmsController');

      /*
      * Warehouses
      **/
      Route::resource('whs','SWMS\SWarehousesController');
      Route::get('whs/{id}/destroy',[
        'uses' => 'SWMS\SWarehousesController@Destroy',
        'as' => 'wms.whs.destroy'
      ]);
      Route::get('whs/{id}/activate', [
        'uses' => 'SWMS\SWarehousesController@Activate',
        'as' => 'wms.whs.activate'
      ]);
      Route::get('whs/{id}/copy', [
        'uses' => 'SWMS\SWarehousesController@Copy',
        'as' => 'wms.whs.copy'
      ]);

      /*
      * Locations
      **/
      Route::resource('locs','SWMS\SLocationsController');
      Route::get('locs/{id}/destroy',[
        'uses' => 'SWMS\SLocationsController@Destroy',
        'as' => 'wms.locs.destroy'
      ]);
      Route::get('locs/{id}/activate', [
        'uses' => 'SWMS\SLocationsController@Activate',
        'as' => 'wms.locs.activate'
      ]);
      Route::get('locs/{id}/copy', [
        'uses' => 'SWMS\SLocationsController@Copy',
        'as' => 'wms.locs.copy'
      ]);

  });


//****************************************/ Shipments /*************************
		Route::get('/tms/home',[
			'as' => 'tms.home',
			'uses' => 'STMS\SShipmentsController@Home'
		]);
		Route::resource('tms','STMS\SShipmentsController');

//****************************************/ Siie /*************************
    Route::group(['prefix' => 'siie'], function () {

      Route::get('/home',[
  			'as' => 'siie.home',
  			'uses' => 'SERP\SSiieController@Home'
  		]);
      Route::resource('central','SERP\SSiieController');

      Route::resource('companies','SERP\SSiieCompaniesController');
      Route::get('companies/{id}/destroy',[
        'uses' => 'SERP\SSiieCompaniesController@Destroy',
        'as' => 'siie.companies.destroy'
      ]);
      Route::get('companies/{id}/activate', [
        'uses' => 'SERP\SSiieCompaniesController@Activate',
        'as' => 'siie.companies.activate'
      ]);

      Route::resource('branches','SERP\SBranchesController');
      Route::get('branches/{id}/destroy',[
        'uses' => 'SERP\SBranchesController@Destroy',
        'as' => 'siie.branches.destroy'
      ]);
      Route::get('branches/{id}/activate', [
        'uses' => 'SERP\SBranchesController@Activate',
        'as' => 'siie.branches.activate'
      ]);

      Route::resource('years','SERP\SYearsController');
      Route::get('years/{id}/destroy',[
        'uses' => 'SERP\SYearsController@Destroy',
        'as' => 'siie.years.destroy'
      ]);
      Route::get('years/{id}/activate', [
        'uses' => 'SERP\SYearsController@Activate',
        'as' => 'siie.years.activate'
      ]);

      Route::resource('months','SERP\SMonthsController');
      Route::get('months/{year}/index',[
        'uses' => 'SERP\SMonthsController@index',
        'as' => 'siie.months.index'
      ]);

      Route::resource('bps','SERP\SPartnersController');
      Route::get('bps/{id}/destroy',[
        'uses' => 'SERP\SPartnersController@Destroy',
        'as' => 'siie.bps.destroy'
      ]);
      Route::get('bps/{id}/activate', [
        'uses' => 'SERP\SPartnersController@Activate',
        'as' => 'siie.bps.activate'
      ]);
      Route::get('bps/{id}/copy', [
  			'uses' => 'SERP\SPartnersController@Copy',
  			'as' => 'siie.bps.copy'
  		]);

      /*
      * Units
      **/

      Route::resource('units','SERP\SUnitsController');
      Route::get('units/{id}/destroy',[
        'uses' => 'SERP\SUnitsController@Destroy',
        'as' => 'siie.units.destroy'
      ]);
      Route::get('units/{id}/activate', [
        'uses' => 'SERP\SUnitsController@Activate',
        'as' => 'siie.units.activate'
      ]);
      Route::get('units/{id}/copy', [
        'uses' => 'SERP\SUnitsController@Copy',
        'as' => 'siie.units.copy'
      ]);

      /*
      * Families
      **/
      Route::resource('families','SERP\SFamiliesController');
      Route::get('families/{id}/destroy',[
        'uses' => 'SERP\SFamiliesController@Destroy',
        'as' => 'siie.families.destroy'
      ]);
      Route::get('families/{id}/activate', [
        'uses' => 'SERP\SFamiliesController@Activate',
        'as' => 'siie.families.activate'
      ]);
      Route::get('families/{id}/copy', [
        'uses' => 'SERP\SFamiliesController@Copy',
        'as' => 'siie.families.copy'
      ]);

      /*
      * Groups
      **/
      Route::resource('groups','SERP\SGroupsController');
      Route::get('groups/{id}/destroy',[
        'uses' => 'SERP\SGroupsController@Destroy',
        'as' => 'siie.groups.destroy'
      ]);
      Route::get('groups/{id}/activate', [
        'uses' => 'SERP\SGroupsController@Activate',
        'as' => 'siie.groups.activate'
      ]);
      Route::get('groups/{id}/copy', [
        'uses' => 'SERP\SGroupsController@Copy',
        'as' => 'siie.groups.copy'
      ]);

      /*
      * Genders
      **/
      Route::resource('genders','SERP\SGendersController');
      Route::get('genders/{id}', [
      	'uses' => 'SERP\SGendersController@children',
      	'as' => 'siie.genders'
      ]);
    });

	});
});

Route::get('auth/login', [
	'uses' => 'Auth\AuthController@getLogin',
	'as'   => 'auth.login'
]);
Route::post('auth/login', [
	'uses' => 'Auth\AuthController@postLogin',
	'as'   => 'auth.login'
]);
Route::get('auth/logout', [
	'uses' => 'Auth\AuthController@getLogout',
	'as'   => 'auth.logout'
]);
