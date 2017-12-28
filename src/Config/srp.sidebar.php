<?PHP 

return [
    'srp' => [
        'permission'    => 'srp.request',
        'name'          => 'Ship Replacement Program',
        'icon'          => 'fa-rocket',
        'route_segment' => 'srp',
        'route'         => 'srp.request',
    ],
    'srpadmin' => [
        'permission'    => 'srp.settle',
        'name'          => 'SRP Administration',
        'icon'          => 'fa-rocket',
        'route_segment' => 'srp',
        'route'         => 'srpadmin.list',
    ],
];
