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

Route::get('/notauthorizedsystem', ['as' => 'notauthorizedsys',
function () {
    return view('errors.401_1');
}]);

Route::get('/notauthorized', ['as' => 'notauthorized',
function () {
    return view('errors.401');
}]);

Route::group(['middleware' => ['auth']], function() {

//****************************************/ Start/*************************

	Route::resource('start','SYS\SStartController');
	Route::get('/start',[
		'as' => 'start',
		'uses' => 'SYS\SStartController@index'
	]);
	Route::post('/start/in',[
		'as' => 'start.getIn',
		'uses' => 'SYS\SStartController@GetIn'
	]);

//****************************************/ Admin/*************************
	Route::group(['middleware' => ['mdadmin']], function() {

		Route::get('/admin',[
			'as' => 'plantilla.admin',
			'uses' => 'SPlantillaController@index'
		]);

  Route::group(['prefix' => 'admin', 'middleware' => ['mdadmin']], function () {

      /*
      * Users
      **/
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

      /*
      * privileges
      **/

  		Route::resource('privileges','SYS\SPrivilegesController');
  		Route::get('privileges/{id}/activate', [
  			'uses' => 'SYS\SPrivilegesController@Activate',
  			'as' => 'admin.privileges.activate'
  		]);
  		Route::get('privileges/{id}/destroy',[
  			'uses' => 'SYS\SPrivilegesController@Destroy',
  			'as' => 'admin.privileges.destroy'
  		]);

      /*
      * Permissions
      **/
  		Route::resource('permissions','SYS\SPermissionsController');
  		Route::get('permissions/{id}/activate', [
  			'uses' => 'SYS\SPermissionsController@Activate',
  			'as' => 'admin.permissions.activate'
  		]);
  		Route::get('permissions/{id}/destroy',[
  			'uses' => 'SYS\SPermissionsController@Destroy',
  			'as' => 'admin.permissions.destroy'
  		]);

      /*
      * userPermissions
      **/

  		Route::resource('userPermissions','SYS\SUserPermissionsController');
  		Route::get('userPermissions/{id}/activate', [
  			'uses' => 'SYS\SUserPermissionsController@Activate',
  			'as' => 'admin.userPermissions.activate'
  		]);
  		Route::get('userPermissions/{id}/destroy',[
  			'uses' => 'SYS\SUserPermissionsController@Destroy',
  			'as' => 'admin.userPermissions.destroy'
  		]);

      /*
      * Companies
      **/

      Route::resource('companies','SYS\SCompaniesController');
      Route::get('companies/{id}/destroy',[
  			'uses' => 'SYS\SCompaniesController@Destroy',
  			'as' => 'admin.companies.destroy'
  		]);
      Route::get('companies/{id}/activate', [
  			'uses' => 'SYS\SCompaniesController@Activate',
  			'as' => 'admin.companies.activate'
  		]);

      Route::resource('usraccess','SYS\SUserCompaniesController');
    });

	});

//****************************************/ Company /*************************

	Route::group(['middleware' => ['mdcompany']], function() {

	Route::group(['middleware' => ['mdstandard']], function() { //** standard middleware

		Route::get('/modules',[
			'as' => 'start.selmod',
			'uses' => 'SYS\SStartController@SelectModule'
		]);

//****************************************/ Manufacturing /*************************
		Route::get('/mms/home',[
			'as' => 'mms.home',
			'uses' => 'MMS\SProductionController@Home'
		]);
		Route::resource('mms','MMS\SProductionController');

//****************************************/ Quality Module /*************************
		Route::get('/qms/home',[
			'as' => 'qms.home',
			'uses' => 'QMS\SQualityController@Home'
		]);
		Route::resource('qms','QMS\SQualityController');

//****************************************/ Warehouses Module/*************************

  Route::group(['prefix' => 'wms'], function () {
  		Route::get('/home',[
  			'as' => 'wms.home',
  			'uses' => 'WMS\SWmsController@Home'
  		]);
  		Route::resource('wms','WMS\SWmsController');

      /*
      * Warehouses
      **/
      Route::resource('whs','WMS\SWarehousesController');
      Route::get('whs/{id}/destroy',[
        'uses' => 'WMS\SWarehousesController@Destroy',
        'as' => 'wms.whs.destroy'
      ]);
      Route::get('whs/{id}/activate', [
        'uses' => 'WMS\SWarehousesController@Activate',
        'as' => 'wms.whs.activate'
      ]);
      Route::get('whs/{id}/copy', [
        'uses' => 'WMS\SWarehousesController@Copy',
        'as' => 'wms.whs.copy'
      ]);

      /*
      * Locations
      **/
      Route::resource('locs','WMS\SLocationsController');
      Route::get('locs/{id}/destroy',[
        'uses' => 'WMS\SLocationsController@Destroy',
        'as' => 'wms.locs.destroy'
      ]);
      Route::get('locs/{id}/activate', [
        'uses' => 'WMS\SLocationsController@Activate',
        'as' => 'wms.locs.activate'
      ]);
      Route::get('locs/{id}/copy', [
        'uses' => 'WMS\SLocationsController@Copy',
        'as' => 'wms.locs.copy'
      ]);

      /*
      * Iventory movements
      **/
      Route::get('/movs/view', [
      	'uses' => 'WMS\SMovsController@index',
      	'as' => 'wms.movs.index'
      ]);
      Route::post('/movs', [
      	'uses' => 'WMS\SMovsController@store',
      	'as' => 'wms.movs.store'
      ]);
      Route::get('/movs/{id?}/create', [
      	'uses' => 'WMS\SMovsController@create',
      	'as' => 'wms.movs.create'
      ]);
      Route::get('/movs/{id?}/create/children', [
      	'uses' => 'WMS\SMovsController@children',
      	'as' => 'wms.movs.create.children'
      ]);
      Route::post('/movs/{id?}/create/storetable', [
      	'uses' => 'WMS\SMovsController@getTable',
      	'as' => 'wms.movs.create.storetable'
      ]);

      /*
      * Stock
      **/
      Route::get('/stock/{id}', [
      	'uses' => 'WMS\SStockController@index',
      	'as' => 'wms.stock.index'
      ]);

      /*
      * Barcodes
      **/

      // Route::get('/codes/start', 'WMS\SCodesController@start');
      Route::get('/codes/start',[
        'uses' => 'WMS\SCodesController@start',
        'as'   => 'wms.codes.start'
      ]);

      Route::get('/codes/findProductName','WMS\SCodesController@findProductName');

      Route::post('/codes/generate', [
        'uses' => 'WMS\SCodesController@generate',
        'as' => 'wms.codes.generate'
      ]);

      Route::get('/codes/consultBarcode',[
        'uses' => 'WMS\SCodesController@consultBarcode',
        'as' => 'wms.codes.consult'
      ]);

      Route::post('/codes/decode', [
        'uses' => 'WMS\SCodesController@decode',
        'as' => 'wms.codes.decode'
      ]);

      /*
      * Lots
      **/
      Route::resource('lots','WMS\SWmsLotsController');
      Route::get('lots/{id}/destroy',[
        'uses' => 'WMS\SWmsLotsController@Destroy',
        'as'   => 'wms.lots.destroy'
      ]);
      Route::get('lots/{id}/active',[
        'uses' => 'WMS\SWmsLotsController@Activate',
        'as'   => 'wms.lots.activate'
      ]);
      Route::get('lots/{id}/copy', [
        'uses' => 'WMS\SWmsLotsController@Copy',
        'as'   => 'wms.lots.copy'
      ]);

      /*
      * Pallets
      **/
      Route::resource('pallets','WMS\SPalletsController');
      Route::get('pallets/{id}/destroy',[
        'uses' => 'WMS\SPalletsController@Destroy',
        'as'   => 'wms.pallets.destroy'
      ]);
      Route::get('pallets/{id}/active',[
        'uses' => 'WMS\SPalletsController@Activate',
        'as'   => 'wms.pallets.activate'
      ]);
      Route::get('lots/{id}/copy', [
        'uses' => 'WMS\SPalletsController@Copy',
        'as'   => 'wms.pallets.copy'
      ]);

  });


//****************************************/ Shipments /*************************
		Route::get('/tms/home',[
			'as' => 'tms.home',
			'uses' => 'TMS\SShipmentsController@Home'
		]);
		Route::resource('tms','TMS\SShipmentsController');

  }); //** standard middleware

//****************************************/ Siie /*************************
    Route::group(['prefix' => 'siie', 'middleware' => ['mdmanager']], function () {

      Route::get('/home',[
  			'as' => 'siie.home',
  			'uses' => 'ERP\SSiieController@Home'
  		]);
      Route::resource('central','ERP\SSiieController');

      /*
      * branches
      **/

      Route::resource('branches','ERP\SBranchesController');
      Route::get('branches/{id}/destroy',[
        'uses' => 'ERP\SBranchesController@Destroy',
        'as' => 'siie.branches.destroy'
      ]);
      Route::get('branches/{id}/activate', [
        'uses' => 'ERP\SBranchesController@Activate',
        'as' => 'siie.branches.activate'
      ]);
      Route::get('branches/{id}/create', [
        'uses' => 'ERP\SBranchesController@create',
        'as' => 'siie.branches.create'
      ]);

      /*
      * address
      **/

      Route::resource('address','ERP\SAddressController');
      Route::get('siie/address/{id?}', [
        'as' => 'siie.address.index',
    		'uses' => 'ERP\SAddressController@Index'
    		]);
      Route::get('address/{id}/destroy',[
        'uses' => 'ERP\SAddressController@Destroy',
        'as' => 'siie.address.destroy'
      ]);
      Route::get('address/{id}/activate', [
        'uses' => 'ERP\SAddressController@Activate',
        'as' => 'siie.address.activate'
      ]);
      Route::get('/address/create/children', [
      	'uses' => 'ERP\SAddressController@children',
      	'as' => 'siie.address.create.children'
      ]);
      Route::get('/address/{id?}/edit/children', [
      	'uses' => 'ERP\SAddressController@children',
      	'as' => 'siie.address.edit.children'
      ]);

      /*
      * Years and months
      **/
      Route::resource('years','ERP\SYearsController');
      Route::get('years/{id}/destroy',[
        'uses' => 'ERP\SYearsController@Destroy',
        'as' => 'siie.years.destroy'
      ]);
      Route::get('years/{id}/activate', [
        'uses' => 'ERP\SYearsController@Activate',
        'as' => 'siie.years.activate'
      ]);

      Route::resource('months','ERP\SMonthsController');
      Route::get('months/{year}/index',[
        'uses' => 'ERP\SMonthsController@index',
        'as' => 'siie.months.index'
      ]);

      /*
      * Partners
      **/

      Route::resource('bps','ERP\SPartnersController');
      Route::get('bps/{id}/destroy',[
        'uses' => 'ERP\SPartnersController@Destroy',
        'as' => 'siie.bps.destroy'
      ]);
      Route::get('bps/{id}/activate', [
        'uses' => 'ERP\SPartnersController@Activate',
        'as' => 'siie.bps.activate'
      ]);
      Route::get('bps/{id}/copy', [
  			'uses' => 'ERP\SPartnersController@Copy',
  			'as' => 'siie.bps.copy'
  		]);

      /*
      * Units
      **/

      Route::resource('units','ERP\SUnitsController');
      Route::get('units/{id}/destroy',[
        'uses' => 'ERP\SUnitsController@Destroy',
        'as' => 'siie.units.destroy'
      ]);
      Route::get('units/{id}/activate', [
        'uses' => 'ERP\SUnitsController@Activate',
        'as' => 'siie.units.activate'
      ]);
      Route::get('units/{id}/copy', [
        'uses' => 'ERP\SUnitsController@Copy',
        'as' => 'siie.units.copy'
      ]);

      /*
      * Families
      **/
      Route::resource('families','ERP\SFamiliesController');
      Route::get('families/{id}/destroy',[
        'uses' => 'ERP\SFamiliesController@Destroy',
        'as' => 'siie.families.destroy'
      ]);
      Route::get('families/{id}/activate', [
        'uses' => 'ERP\SFamiliesController@Activate',
        'as' => 'siie.families.activate'
      ]);
      Route::get('families/{id}/copy', [
        'uses' => 'ERP\SFamiliesController@Copy',
        'as' => 'siie.families.copy'
      ]);

      /*
      * Groups
      **/
      Route::resource('groups','ERP\SGroupsController');
      Route::get('groups/{id}/destroy',[
        'uses' => 'ERP\SGroupsController@Destroy',
        'as' => 'siie.groups.destroy'
      ]);
      Route::get('groups/{id}/activate', [
        'uses' => 'ERP\SGroupsController@Activate',
        'as' => 'siie.groups.activate'
      ]);
      Route::get('groups/{id}/copy', [
        'uses' => 'ERP\SGroupsController@Copy',
        'as' => 'siie.groups.copy'
      ]);

      /*
      * Genders
      **/
      Route::resource('genders','ERP\SGendersController', ['except' => ['index']]);
      Route::get('siie/genders/{id?}', [
        'uses' => 'ERP\SGendersController@index',
        'as' => 'siie.genders.index'
      ]);
      Route::get('genders/{id}/destroy',[
        'uses' => 'ERP\SGendersController@Destroy',
        'as' => 'siie.genders.destroy'
      ]);
      Route::get('genders/{id}/activate', [
        'uses' => 'ERP\SGendersController@Activate',
        'as' => 'siie.genders.activate'
      ]);
      Route::get('genders/{id}/copy', [
        'uses' => 'ERP\SGendersController@Copy',
        'as' => 'siie.genders.copy'
      ]);
      Route::get('/genders/create/children', [
      	'uses' => 'ERP\SGendersController@children',
      	'as' => 'siie.genders.create.children'
      ]);
      Route::get('/genders/{id?}/edit/children', [
      	'uses' => 'ERP\SGendersController@children',
      	'as' => 'siie.genders.edit.children'
      ]);

      /*
      * Items
      **/
      Route::resource('items','ERP\SItemsController');
      Route::get('siie/items/{id?}', [
        'as' => 'siie.items.index',
        'uses' => 'ERP\SItemsController@Index'
      ]);
      Route::get('items/{id}/destroy',[
        'uses' => 'ERP\SItemsController@Destroy',
        'as' => 'siie.items.destroy'
      ]);
      Route::get('items/{id}/activate', [
        'uses' => 'ERP\SItemsController@Activate',
        'as' => 'siie.items.activate'
      ]);
      Route::get('items/{id}/copy', [
        'uses' => 'ERP\SItemsController@Copy',
        'as' => 'siie.items.copy'
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
