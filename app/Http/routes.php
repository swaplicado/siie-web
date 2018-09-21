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
    \Debugbar::disable();
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

Route::get('/start/branchwhs',[
'as' => 'start.branchwhs',
'uses' => 'SYS\SStartController@branchwhs'
]);
Route::get('/start/selectwhs',[
'as' => 'start.selectwhs',
'uses' => 'SYS\SStartController@selectwhs'
]);
Route::resource('start','SYS\SStartController');
Route::get('/start',[
  'as' => 'start',
  'uses' => 'SYS\SStartController@index'
]);
Route::post('/start/in',[
  'as' => 'start.getIn',
  'uses' => 'SYS\SStartController@GetIn'
]);
Route::post('/start/branch',[
  'as' => 'start.branch',
  'uses' => 'SYS\SStartController@branch'
]);
Route::post('/start/whs',[
  'as' => 'start.whs',
  'uses' => 'SYS\SStartController@whs'
]);
Route::get('manage/{id}/changepass',[
  'uses' => 'SPassController@ChangePass',
  'as' => 'manage.users.changepass'
]);
Route::put('manage/{id}/updatepass',[
  'uses' => 'SPassController@UpdatePass',
  'as' => 'manage.users.updatepass'
]);
Route::post('manage/changedate',[
  'uses' => 'SPassController@changeDate',
  'as' => 'manage.changedate'
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
  			'uses' => 'SPassController@ChangePass',
  			'as' => 'admin.users.changepass'
  		]);
  		Route::put('users/{id}/updatepass',[
  			'uses' => 'SPassController@UpdatePass',
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
        * userpermissions
      */
       Route::get('userpermissions/findPermission','SYS\SUserPermissionsController@findPermission');
       Route::get('userpermissions/findCompanies','SYS\SUserPermissionsController@findCompanies');
       Route::get('userpermissions/findBranches','SYS\SUserPermissionsController@findBranches');
       Route::resource('userpermissions','SYS\SUserPermissionsController');
       Route::get('userpermissions/{id}/creator', [
    			'uses' => 'SYS\SUserPermissionsController@Creator',
    			'as' => 'admin.userpermissions.creator'
    		]);
   		Route::get('userpermissions/{id}/destroy',[
   			'uses' => 'SYS\SUserPermissionsController@Destroy',
   			'as' => 'admin.userpermissions.destroy'
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

      /*
      * User companies
      **/

      Route::resource('usraccess','SYS\SUserCompaniesController');

      /*
      * User Branches
      **/
      Route::resource('userBranches','ERP\SUserBranchesController');

      /*
      * User Warehouses
      **/
      Route::get('userwhs/findWhs','ERP\SUserWhsController@findWhs');
      Route::resource('userwhs','ERP\SUserWhsController');
      Route::get('userwhs/{id}/edit', [
        'uses' => 'ERP\SUserWhsController@edit',
        'as' => 'admin.userwhs.edit'
      ]);

    });

	});

//****************************************/ Company /*************************

	Route::group(['middleware' => ['mdcompany']], function() {

	Route::group(['middleware' => ['mdstandard']], function() { //** standard middleware

		Route::get('/modules',[
			'as' => 'start.selmod',
			'uses' => 'SYS\SStartController@SelectModule'
		]);

//****************************************/ Manufacturing Module /*************************

    Route::group(['prefix' => 'mms'], function () {
      Route::get('/home',[
        'as' => 'mms.home',
        'uses' => 'MMS\SProductionController@Home'
      ]);

      /*
      * Formulas
      **/
      Route::get('/formulas/viewdetail', [
        'uses' => 'MMS\SFormulasController@getDetail',
        'as' => 'mms.formulas.indexdetail'
      ]);
      Route::resource('formulas','MMS\SFormulasController');
      Route::get('/formulas/create/itemformulas', [
      	'uses' => 'MMS\SFormulasController@getItemFormulas',
      	'as' => 'mms.formulas.create.itemformulas'
      ]);
      Route::get('/formulas/{id}/edit/itemformulas', [
      	'uses' => 'MMS\SFormulasController@getItemFormulas',
      	'as' => 'mms.formulas.edit.itemformulas'
      ]);
      Route::get('/formulas/create/{id?}', [
      	'uses' => 'MMS\SFormulasController@create',
      	'as' => 'mms.formulas.create'
      ]);
      Route::get('formulas/{id}/copy', [
        'uses' => 'MMS\SFormulasController@Copy',
        'as' => 'mms.formulas.copy'
      ]);
      Route::get('formulas/{id}/destroy',[
        'uses' => 'MMS\SFormulasController@Destroy',
        'as' => 'mms.formulas.destroy'
      ]);
      Route::get('formulas/{id}/activate', [
  			'uses' => 'MMS\SFormulasController@Activate',
  			'as' => 'mms.formulas.activate'
  		]);
      Route::get('formulas/{id}/print', [
  			'uses' => 'MMS\SFormulasController@Print',
  			'as' => 'mms.formulas.print'
  		]);

      /*
      * Production planes
      **/
      Route::resource('planes','MMS\SProductionPlanesController');
      Route::get('planes/{id}/destroy',[
  			'uses' => 'MMS\SProductionPlanesController@Destroy',
  			'as' => 'mms.planes.destroy'
  		]);
      Route::get('planes/{id}/activate', [
  			'uses' => 'MMS\SProductionPlanesController@Activate',
  			'as' => 'mms.planes.activate'
  		]);
      Route::get('planes/index/{folio?}',[
        'uses' => 'MMS\SProductionPlanesController@index',
        'as' => 'mms.planes.index'
      ]);

      /*
      * floor
      **/
      Route::resource('floors','MMS\SFloorsController');
      Route::get('floors/{id}/destroy',[
        'uses' => 'MMS\SFloorsController@Destroy',
        'as'   => 'mms.floors.destroy'
      ]);
      Route::get('floors/{id}/active',[
        'uses' => 'MMS\SFloorsController@Activate',
        'as'   => 'mms.floors.activate'
      ]);
      Route::get('floors/{id}/copy', [
        'uses' => 'MMS\SFloorsController@Copy',
        'as'   => 'mms.floors.copy'
      ]);

      /*
      * production order
      **/
      Route::get('orders/findFormulas','MMS\SProductionOrdersController@findFormulas');
      Route::resource('orders','MMS\SProductionOrdersController');
      Route::get('orders/{id}/destroy',[
        'uses' => 'MMS\SProductionOrdersController@Destroy',
        'as'   => 'mms.orders.destroy'
      ]);
      Route::get('orders/{id}/active',[
        'uses' => 'MMS\SProductionOrdersController@Activate',
        'as'   => 'mms.orders.activate'
      ]);
      Route::get('orders/{id}/copy', [
        'uses' => 'MMS\SProductionOrdersController@Copy',
        'as'   => 'mms.orders.copy'
      ]);
      Route::get('orders/{id}/next', [
        'uses' => 'MMS\SProductionOrdersController@Next',
        'as'   => 'mms.orders.next'
      ]);
      Route::get('orders/{id}/previous', [
        'uses' => 'MMS\SProductionOrdersController@Previous',
        'as'   => 'mms.orders.previous'
      ]);
      Route::get('orders/{id}/kardex', [
        'uses' => 'MMS\SProductionOrdersController@GetKardex',
        'as'   => 'mms.orders.kardex'
      ]);
      Route::get('orders/{id}/consumptions', [
        'uses' => 'MMS\SProductionOrdersController@GetConsumptions',
        'as'   => 'mms.orders.consumptions'
      ]);
      Route::get('orders/{id}/consume', [
        'uses' => 'MMS\SProductionOrdersController@Consume',
        'as'   => 'mms.orders.consume'
      ]);
      Route::get('orders/{id}/print', [
        'uses' => 'MMS\SProductionOrdersController@Print',
        'as'   => 'mms.orders.print'
      ]);
      Route::get('orders/{id}/details', [
        'uses' => 'MMS\SProductionOrdersController@GetOrderDetail',
        'as'   => 'mms.orders.details'
      ]);

      /*
      * Explosion of materials
      **/
      Route::resource('explosion','MMS\SExplosionMaterialsController');

      /*
      * Production movements query
      **/
      Route::get('movs/{queryType}/{title}/show',[
        'uses' => 'MMS\SMovsQuerysController@show',
        'as'   => 'mms.movs.show'
      ]);

    });


//****************************************/ Quality Module /*************************

Route::group(['prefix' => 'qms'], function () {
  		Route::get('/home',[
  			'as' => 'qms.home',
  			'uses' => 'QMS\SQualityController@Home'
  		]);

      Route::get('/segregation/findWarehouse','QMS\SSegregationsController@findWarehouse');
      Route::get('/segregation/findLocations','QMS\SSegregationsController@findLocations');
      Route::get('/segregation/binnacle',[
       'uses' =>  'QMS\SSegregationsController@binnacle',
       'as' => 'qms.segregations.binnacle'
     ]);

     /*
      * segregations
      **/
      // Route::resource('segregations','WMS\SSegregationsController');
      Route::resource('qms','QMS\SQualityController');
      Route::get('segregations/{title}/{segType}/{viewType}/{typeView}/index',[
        'uses' => 'QMS\SSegregationsController@index',
        'as' => 'qms.segregations.index'
      ]);

      Route::get('segregations/{title}/{type}/consult',[
        'uses' => 'QMS\SSegregationsController@consult',
        'as' => 'qms.segregations.consult'
      ]);


      Route::post('segregations/{title}/{segType}/{viewType}/{typeView}/index/process',[
        'uses' => 'QMS\SSegregationsController@Process',
        'as' => 'qms.segregations.index.process'
      ]);

      Route::post('segregations/toQuarentine',[
        'uses' => 'QMS\SSegregationsController@toQuarentine',
        'as'   => 'qms.segregations.toQuarentine'
      ]);

      Route::post('segregations/toRelease',[
        'uses' => 'QMS\SSegregationsController@toRelease',
        'as'   => 'qms.segregations.toRelease'
      ]);

      Route::post('segregations/toRefuse',[
        'uses' => 'QMS\SSegregationsController@toRefuse',
        'as'   => 'qms.segregations.toRefuse'
      ]);

      Route::post('segregations/prepareData',[
        'uses' => 'QMS\SSegregationsController@prepareData',
        'as'   => 'qms.segregations.prepareData'
      ]);
  });

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
      Route::get('locations/{id}/barcode', [
        'uses' => 'WMS\SLocationsController@Barcode',
        'as'   => 'wms.locations.barcode'
      ]);
      /*
      * Limits
      **/
      Route::resource('limits','WMS\SLimitsController');

      /*
      * Item containers
      **/
      Route::resource('itemcontainers','WMS\SItemContainersController');
      Route::get('itemcontainers/{id}/destroy',[
        'uses' => 'WMS\SItemContainersController@Destroy',
        'as' => 'wms.itemcontainers.destroy'
      ]);
      Route::get('itemcontainers/{id}/activate', [
        'uses' => 'WMS\SItemContainersController@Activate',
        'as' => 'wms.itemcontainers.activate'
      ]);
      Route::get('itemcontainers/{id}/copy', [
        'uses' => 'WMS\SItemContainersController@Copy',
        'as' => 'wms.itemcontainers.copy'
      ]);

      /*
      * Iventory movements
      **/
      Route::get('/movs/view/{folio?}', [
      	'uses' => 'WMS\SMovsController@index',
      	'as' => 'wms.movs.index'
      ]);
      Route::get('/movs/viewdetail', [
      	'uses' => 'WMS\SMovsController@movementsIndex',
      	'as' => 'wms.movs.indexdetail'
      ]);
      Route::get('/movs/docs', [
      	'uses' => 'WMS\SMovsController@inventoryDocs',
      	'as' => 'wms.movs.docs'
      ]);
      Route::get('/movs/receptions', [
      	'uses' => 'WMS\SMovsController@receiveMovsIndex',
      	'as' => 'wms.movs.receptions'
      ]);
      Route::get('/movs/transferred', [
      	'uses' => 'WMS\SMovsController@getTransferred',
      	'as' => 'wms.movs.transferred'
      ]);
      Route::get('/movs/received', [
      	'uses' => 'WMS\SMovsController@getReceived',
      	'as' => 'wms.movs.received'
      ]);
      Route::get('/movs/receivetransfer/{idMov}', [
      	'uses' => 'WMS\SMovsController@receiveTransfer',
      	'as' => 'wms.movs.receivetransfer'
      ]);
      Route::get('/movs/{id}/{title}/{doc}/create', [
      	'uses' => 'WMS\SMovsController@create',
      	'as' => 'wms.movs.create'
      ]);
      Route::get('/movs/{id}/edit', [
      	'uses' => 'WMS\SMovsController@edit',
      	'as' => 'wms.movs.edit'
      ]);
      Route::get('/movs/{id}/destroy', [
        'uses' => 'WMS\SMovsController@Destroy',
        'as' => 'wms.movs.destroy'
      ]);
      Route::get('/movs/{id}/activate', [
        'uses' => 'WMS\SMovsController@Activate',
        'as' => 'wms.movs.activate'
      ]);
      Route::post('/movs', [
        'uses' => 'WMS\SMovsController@store',
        'as' => 'wms.movs.store'
      ]);
      Route::post('/movs/{id}', [
        'uses' => 'WMS\SMovsController@update',
        'as' => 'wms.movs.update'
      ]);
      Route::get('/movs/{id?}/{title}/{doc}/create/data', [
        'uses' => 'WMS\SMovsController@getMovementData',
        'as' => 'wms.movs.create.data'
      ]);
      Route::get('/movs/{id?}/{title}/{doc}/create/search', [
        'uses' => 'WMS\SMovsController@searchElement',
        'as' => 'wms.movs.create.search'
      ]);
      Route::post('/movs/{id?}/{title}/{doc}/create/validaterow', [
        'uses' => 'WMS\SMovsController@validateRow',
        'as' => 'wms.movs.create.validaterow'
      ]);
      Route::get('/movs/{id}/{title}/{doc}/supply', [
      	'uses' => 'WMS\SMovsController@create',
      	'as' => 'wms.movs.supply'
      ]);
      Route::get('/movs/{id}/{title}/{doc}/supply/data', [
        'uses' => 'WMS\SMovsController@getMovementData',
        'as' => 'wms.movs.supply.data'
      ]);
      Route::get('/movs/{id}/{title}/{doc}/supply/search', [
        'uses' => 'WMS\SMovsController@searchElement',
        'as' => 'wms.movs.supply.search'
      ]);
      Route::post('/movs/{id}/{title}/{doc}/supply/validaterow', [
        'uses' => 'WMS\SMovsController@validateRow',
        'as' => 'wms.movs.supply.validaterow'
      ]);
      Route::get('/movs/{id}/edit/data', [
        'uses' => 'WMS\SMovsController@getMovementData',
        'as' => 'wms.movs.edit.data'
      ]);
      Route::get('/movs/{id}/edit/search', [
        'uses' => 'WMS\SMovsController@searchElement',
        'as' => 'wms.movs.edit.search'
      ]);
      Route::post('/movs/{id}/edit/validaterow', [
        'uses' => 'WMS\SMovsController@validateRow',
        'as' => 'wms.movs.edit.validaterow'
      ]);
      Route::get('/movs/receivetransfer/{idMov}/data', [
      	'uses' => 'WMS\SMovsController@getMovementData',
      	'as' => 'wms.movs.receivetransfer.data'
      ]);
      Route::get('/movs/print/{idMov}', [
      	'uses' => 'WMS\SMovsController@print',
      	'as' => 'wms.movs.print'
      ]);
      Route::get('/movs/{id?}/{title}/{doc}/create/productiondata', [
        'uses' => 'WMS\SMovsController@getProductionData',
        'as' => 'wms.movs.create.productiondata'
      ]);

      /*
      * Stock
      **/
      Route::get('/stock/{title}/{id}', [
      	'uses' => 'WMS\SStockController@index',
      	'as' => 'wms.stock.index'
      ]);
      Route::get('/stock/{title}/{id}/movements', [
      	'uses' => 'WMS\SStockController@getMovements',
      	'as' => 'wms.stock.index.movements'
      ]);

      /*
      * Warehouses inventory
      **/
      Route::get('/inventory/emptywarehouse', [
      	'uses' => 'WMS\SInventoriesController@emptyWarehouseIndex',
      	'as' => 'wms.inventory.emptywarehouse'
      ]);
      Route::get('/inventory/emptywarehouse/stock', [
      	'uses' => 'WMS\SInventoriesController@getStock',
      	'as' => 'wms.inventory.emptywarehouse.stock'
      ]);
      Route::get('/inventory/initialinventory', [
      	'uses' => 'WMS\SInventoriesController@initialIndex',
      	'as' => 'wms.inventory.initialinventory'
      ]);
      Route::post('/inventory/initialinventory/store',[
        'uses' => 'WMS\SInventoriesController@generateInitialInventory',
        'as' => 'wms.inventory.initialinventory.store'
      ]);
      Route::get('/inventory/physicalinventory', [
        'uses' => 'WMS\SInventoriesController@physicalInventory',
        'as' => 'wms.inventory.physicalinventory'
      ]);
      Route::get('/inventory/physicalinventory/data', [
        'uses' => 'WMS\SMovsController@getMovementData',
        'as' => 'wms.inventory.physicalinventory.data'
      ]);
      Route::post('/inventory/physicalinventory/validaterow', [
        'uses' => 'WMS\SMovsController@validateRow',
        'as' => 'wms.inventory.physicalinventory.validaterow'
      ]);
      Route::get('/inventory/physicalinventory/search', [
        'uses' => 'WMS\SMovsController@searchElement',
        'as' => 'wms.inventory.physicalinventory.search'
      ]);
      Route::get('/inventory/physicalinventory/create', [
        'uses' => 'WMS\SInventoriesController@createPhysicalInventory',
        'as' => 'wms.inventory.physicalinventory.create'
      ]);


      /*
      * Folios
      **/
      Route::resource('folios','WMS\SFoliosController');
      Route::get('folios/{id}/destroy', [
        'uses' => 'WMS\SFoliosController@Destroy',
        'as' => 'wms.folios.destroy'
      ]);
      Route::get('locs/{id}/activate', [
        'uses' => 'WMS\SFoliosController@Activate',
        'as' => 'wms.folios.activate'
      ]);

      /*
      * Barcodes
      **/
      Route::get('traceability/consult',[
        'uses' => 'WMS\STraceabilityController@consult',
        'as' => 'wms.traceability.consult'
      ]);
      Route::post('traceability/getTraceability',[
        'uses' => 'WMS\STraceabilityController@getTraceability',
        'as'   => 'wms.traceability.gettraceability'
      ]);
      // Route::get('/codes/start', 'WMS\SCodesController@start');
      Route::get('/codes/start',[
        'uses' => 'WMS\SCodesController@start',
        'as'   => 'wms.codes.start'
      ]);

      Route::get('/codes/findWhs','WMS\SCodesController@findWhs');


      Route::get('/codes/findProductName','WMS\SCodesController@findProductName');

      Route::post('/codes/generate', [
        'uses' => 'WMS\SCodesController@generate',
        'as' => 'wms.codes.generate'
      ]);

      Route::get('/codes/consultBarcode',[
        'uses' => 'WMS\SCodesController@consultBarcode',
        'as' => 'wms.codes.consult'
      ]);

      Route::get('/codes/consultwithbranch',[
        'uses' => 'WMS\SCodesController@consultwithbranch',
        'as' => 'wms.codes.withbranch'
      ]);


      Route::post('/codes/decode', [
        'uses' => 'WMS\SCodesController@decode',
        'as' => 'wms.codes.decode'
      ]);

      Route::post('/codes/decodewith', [
        'uses' => 'WMS\SCodesController@decodeWith',
        'as' => 'wms.codes.decodewith'
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
      Route::get('lots/{id}/barcode', [
        'uses' => 'WMS\SWmsLotsController@Barcode',
        'as'   => 'wms.lots.barcode'
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
      Route::get('pallets/{id}/barcode', [
        'uses' => 'WMS\SPalletsController@Barcode',
        'as'   => 'wms.pallets.barcode'
      ]);
      Route::get('pallets/{id}/print', [
        'uses' => 'WMS\SPalletsController@Print',
        'as'   => 'wms.pallets.print'
      ]);
      Route::get('pallets/index/{id?}/{sitem?}',[
        'uses' => 'WMS\SPalletsController@index',
        'as' => 'wms.pallets.index'
      ]);

      /*
      * documents
      **/
      Route::get('docs/{category}/{dclass}/{dtype}/{vtype}/{isupp}/{title}/index',[
        'uses' => 'WMS\SDocsBySuppController@ViewDocs',
        'as' => 'wms.docs.index'
      ]);
      Route::get('docs/{oper}/{id}/openclose',[
        'uses' => 'WMS\SDocsBySuppController@OpenAndclose',
        'as' => 'wms.docs.openclose'
      ]);
      Route::get('docs/{doc_src}/{doc_pry}/link',[
        'uses' => 'WMS\SDocsBySuppController@Link',
        'as' => 'wms.docs.link'
      ]);
      Route::post('docs/{doc_src}/{doc_pry}/link/indirectsupplied',[
        'uses' => 'WMS\SDocsBySuppController@GetIndirectSupplied',
        'as' => 'wms.docs.link.indirectsupplied'
      ]);
      Route::post('docs/storelinks',[
        'uses' => 'WMS\SDocsBySuppController@StoreLinks',
        'as' => 'wms.docs.storelinks'
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
    Route::group(['prefix' => 'siie'], function () {

      Route::get('/home',[
  			'as' => 'siie.home',
  			'uses' => 'ERP\SSiieController@Home'
  		]);
      Route::resource('central','ERP\SSiieController');

      /*
      * Importation
      **/

      Route::get('importation/{imported}/{items}/{partners}/{branches}/{adds}/{docs}/{rows1}/{rows2}',[
        'uses' => 'ERP\SImportationsController@index',
        'as' => 'siie.importation'
      ]);
      Route::post('import/docs',[
        'uses' => 'ERP\SImportationsController@importationDocuments',
        'as' => 'siie.import.docs'
      ]);

      /*
      * documents
      **/

      // Route::resource('docs','ERP\SDocumentsController');
      Route::get('docs/{category}/{dclass}/{dtype}/{title}/index',[
        'uses' => 'ERP\SDocumentsController@Index',
        'as' => 'siie.docs.index'
      ]);
      Route::get('docs/{document}/view',[
        'uses' => 'ERP\SDocumentsController@View',
        'as' => 'siie.docs.view'
      ]);

      /*
      * branches
      **/

      Route::resource('branches','ERP\SBranchesController');
      Route::get('branches/index/{bp?}',[
        'uses' => 'ERP\SBranchesController@index',
        'as' => 'siie.branches.index'
      ]);
      Route::get('branches/{id}/create',[
        'uses' => 'ERP\SBranchesController@Create',
        'as' => 'siie.branches.create'
      ]);
      Route::get('branches/{id}/destroy',[
        'uses' => 'ERP\SBranchesController@Destroy',
        'as' => 'siie.branches.destroy'
      ]);
      Route::get('branches/{id}/activate', [
        'uses' => 'ERP\SBranchesController@Activate',
        'as' => 'siie.branches.activate'
      ]);
      Route::get('branches/{id}/edit', [
        'uses' => 'ERP\SBranchesController@create',
        'as' => 'siie.branches.edit'
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
