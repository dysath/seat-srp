<?php
/**
 * User: Warlof Tutsimo <loic.leuilliot@gmail.com>
 * Date: 15/06/2016
 * Time: 22:02
 */

return [
    'request' => [
        'label' => 'Request',
        'description' => 'Ability to lodge SRP Requests',
        'division' => 'military'
    ],
    'settle' => [
        'label' => 'Settle',
        'description' => 'Allows accepting and rejecting SRP Requests',
        'division' => 'financial'
    ],
    'delete' => [
        'label' => 'Delete',
        'description' => 'Allows deleting SRP requests that have been lodged. Note this does not replace rejection of requests',
        'division' => 'financial'
    ],
    'settings' => [
        'label' => 'Settings',
        'description' => 'Allows configuring the global SRP settings',
        'division' => 'financial'
    ]
];

