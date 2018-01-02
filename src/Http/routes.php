<?PHP

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
	        'middleware' => 'bouncer:srp:request',
        ]);
    });
});
