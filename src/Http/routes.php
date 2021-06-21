<?php

Route::group([
    'namespace' => 'Denngarr\Seat\SeatSrp\Http\Controllers',
    'middleware' => ['web', 'auth'],
    'prefix' => 'api/v2/srp/metrics/web'
], function () {

    Route::get('/summary/monthly/{status}/{limit?}', [
        'as' => 'srp.metrics.api.web.summary.monthly',
        'uses' => 'SrpMetricsApiController@getSummaryMonthly',
    ]);

    Route::get('/summary/user/{status}/{group_id?}/{limit?}', [
        'as' => 'srp.metrics.api.web.summary.user',
        'uses' => 'SrpMetricsApiController@getSummaryUser',
    ]);

    Route::get('/top/ship/{status}/{limit?}', [
        'as' => 'srp.metrics.api.web.top.ship',
        'uses' => 'SrpMetricsApiController@getTopShip',
    ]);

    Route::get('/top/user/{status}/{limit?}', [
        'as' => 'srp.metrics.api.web.top.user',
        'uses' => 'SrpMetricsApiController@getTopUser'
    ]);
});

Route::group([
    'namespace' => 'Denngarr\Seat\SeatSrp\Http\Controllers',
    'middleware' => ['api.auth'],
    'prefix' => 'api/v2/srp/metrics'
], function () {
    Route::get('/summary/monthly/{status}/{limit?}', [
        'as' => 'srp.metrics.api.summary.monthly',
        'uses' => 'SrpMetricsApiController@getSummaryMonthly',
    ]);

    Route::get('/summary/user/{status}/{group_id?}/{limit?}', [
        'as' => 'srp.metrics.api.summary.user',
        'uses' => 'SrpMetricsApiController@getSummaryUser',
    ]);

    Route::get('/top/ship/{status}/{limit?}', [
        'as' => 'srp.metrics.api.top.ship',
        'uses' => 'SrpMetricsApiController@getTopShip',
    ]);

    Route::get('/top/user/{status}/{limit?}', [
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
            'middleware' => 'can:srp.request'
        ]);

        Route::get('/getkillmail', [
            'as'   => 'srp.getKillMail',
            'uses' => 'SrpController@srpGetKillMail',
            'middleware' => 'can:srp.request'
        ]);

        Route::post('/savekillmail', [
            'as'   => 'srp.saveKillMail',
            'uses' => 'SrpController@srpSaveKillMail',
            'middleware' => 'can:srp.request'
        ]);

        Route::get('/admin', [
            'as'   => 'srpadmin.list',
            'uses' => 'SrpAdminController@srpGetKillMails',
            'middleware' => 'can:srp.settle'
        ]);

        Route::post('/admin/addreason', [
            'as'   => 'srp.addReason',
            'uses' => 'SrpAdminController@srpAddReason',
            'middleware' => 'can:srp.settle'
        ]);

        Route::get('/admin/{kill_id}/{action}', [
            'as'   => 'srpadmin.settle',
            'uses' => 'SrpAdminController@srpApprove',
            'middleware' => 'can:srp.settle'
        ])->where(['action' => 'Approve|Reject|Paid Out|Pending']);

        Route::get('/insurances/{kill_id}', [
            'as' => 'srp.insurances',
            'uses' => 'SrpController@getInsurances',
            'middleware' => 'can:srp.request',
        ]);

        Route::get('/ping/{kill_id}', [
            'as' => 'srp.ping',
            'uses' => 'SrpController@getPing',
            'middleware' => 'can:srp.request',
        ]);

        Route::get('/reason/{kill_id}', [
            'as' => 'srp.reason',
            'uses' => 'SrpController@getReason',
            'middleware' => 'can:srp.request',
        ]);

        Route::get('/about', [
            'as'   => 'srp.about',
            'uses' => 'SrpController@getAboutView',
            'middleware' => 'can:srp.request'
        ]);

        Route::get('/instructions', [
            'as'   => 'srp.instructions',
            'uses' => 'SrpController@getInstructionsView',
            'middleware' => 'can:srp.request'
        ]);

        Route::get('/test', [
            'as'   => 'srp.testsrp',
            'uses' => 'SrpAdminController@getTestView',
            'middleware' => 'can:srp.settings'
        ]);

        Route::get('/settings', [
            'as'   => 'srp.settings',
            'uses' => 'SrpAdminController@getSrpSettings',
            'middleware' => 'can:srp.settings'
        ]);

        Route::post('/settings', [
            'as'   => 'srp.savesettings',
            'uses' => 'SrpAdminController@saveSrpSettings',
            'middleware' => 'can:srp.settings'
        ]);

        Route::group([
            'middleware' => 'can:srp.settings',
            'prefix' => 'advanced-settings'
        ], function () {

            Route::post('/add-type', [
                'as' => 'srp.adv.type.add',
                'uses' => 'SrpAdminController@saveSrpRule',
            ]);

            Route::delete('/remove/{rule}', [
                'as' => 'srp.adv.remove',
                'uses' => 'SrpAdminController@removeSrpRule',
            ]);

            Route::get('/types', [
                'as' =>'srp.adv.type.get',
                'uses' => 'SrpAdminController@typesData',
            ]);

            Route::get('/groups', [
                'as' =>'srp.adv.group.get',
                'uses' => 'SrpAdminController@groupsData',
            ]);

            Route::post('/defaults', [
                'as'   => 'srp.saveadvdefault',
                'uses' => 'SrpAdminController@saveAdvDefaultSettings',
                'middleware' => 'can:srp.settings'
            ]);


        });

        Route::group([
            'middleware' => 'can:srp.settle',
            'prefix' => 'metrics'
        ], function () {

            Route::get('/{srp_status?}', [
                'as' => 'srp.metrics',
                'uses' => 'SrpMetricsController@getIndex',
            ]);
        });
    });
});
