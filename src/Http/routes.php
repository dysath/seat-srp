<?php

Route::group([
    'namespace' => 'Denngarr\Seat\SeatSrp\Http\Controllers',
    'middleware' => ['web','auth'],
    'prefix' => 'api/v2/srp/metrics/web'
], function(){

    Route::get('/summary/monthly/{status}/{limit?}', [
        'as' => 'srp.metrics.api.web.summary.monthly',
        'uses' => 'SrpMetricsApiController@getSummaryMonthly',
    ]);

    Route::get('/summary/user/{status}/{group_id?}/{limit?}', [
        'as' => 'srp.metrics.api.web.summary.user',
        'uses' => 'SrpMetricsApiController@getSummaryUser',
    ]);

    Route::get('/top/ship/{status}/{limit?}',[
        'as' => 'srp.metrics.api.web.top.ship',
        'uses' => 'SrpMetricsApiController@getTopShip',
    ]);

    Route::get('/top/user/{status}/{limit?}',[
        'as' => 'srp.metrics.api.web.top.user',
        'uses' => 'SrpMetricsApiController@getTopUser'
    ]);
});

Route::group([
    'namespace' => 'Denngarr\Seat\SeatSrp\Http\Controllers',
    'middleware' => ['api.auth'],
    'prefix' => 'api/v2/srp/metrics'
], function(){
    Route::get('/summary/monthly/{status}/{limit?}', [
        'as' => 'srp.metrics.api.summary.monthly',
        'uses' => 'SrpMetricsApiController@getSummaryMonthly',
    ]);

    Route::get('/summary/user/{status}/{group_id?}/{limit?}', [
        'as' => 'srp.metrics.api.summary.user',
        'uses' => 'SrpMetricsApiController@getSummaryUser',
    ]);

    Route::get('/top/ship/{status}/{limit?}',[
        'as' => 'srp.metrics.api.top.ship',
        'uses' => 'SrpMetricsApiController@getTopShip',
    ]);

    Route::get('/top/user/{status}/{limit?}',[
        'as' => 'srp.metrics.api.top.user',
        'uses' => 'SrpMetricsApiController@getTopUser'
    ]);
});


Route::group([
    'namespace' => 'Denngarr\Seat\SeatSrp\Http\Controllers',
    'prefix' => 'srp'
], function () {

    Route::group([
        'middleware' => ['web', 'auth'],
    ], function () {

        Route::get('/', [
            'as'   => 'srp.request',
            'uses' => 'SrpController@srpGetRequests',
            'middleware' => 'bouncer:srp.request'
        ]);

        Route::get('/getkillmail', [
            'as'   => 'srp.getKillMail',
            'uses' => 'SrpController@srpGetKillMail',
            'middleware' => 'bouncer:srp.request'
        ]);

        Route::post('/savekillmail', [
            'as'   => 'srp.saveKillMail',
            'uses' => 'SrpController@srpSaveKillMail',
            'middleware' => 'bouncer:srp.request'
        ]);

        Route::get('/admin', [
            'as'   => 'srpadmin.list',
            'uses' => 'SrpAdminController@srpGetKillMails',
            'middleware' => 'bouncer:srp.settle'
        ]);

        Route::get('/admin/{kill_id}/{action}', [
            'as'   => 'srpadmin.settle',
            'uses' => 'SrpAdminController@srpApprove',
            'middleware' => 'bouncer:srp.settle'
        ])->where(['action' => 'Approve|Reject|Paid Out|Pending']);

        Route::get('/insurances/{kill_id}', [
            'as' => 'srp.insurances',
            'uses' => 'SrpController@getInsurances',
            'middleware' => 'bouncer:srp.request',
        ]);

        Route::get('/ping/{kill_id}', [
        	'as' => 'srp.ping',
	        'uses' => 'SrpController@getPing',
	        'middleware' => 'bouncer:srp.request',
        ]);

        Route::group([
            'middleware' => 'bouncer:srp.settle',
            'prefix' => 'metrics'
        ], function (){

            Route::get('/{srp_status?}', [
                'as' => 'srp.metrics',
                'uses' => 'SrpMetricsController@getIndex',
            ]);
        });
    });
});
