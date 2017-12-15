<?PHP 

return [
    'srp' => [
        'permission'    => 'Superuser',
        'name'          => 'Ship Replacement Program',
        'icon'          => 'fa-rocket',
        'route_segment' => 'srp',
        'route'         => 'srp.request',
    ],
    'srpadmin' => [
        'permission'    => 'Superuser',
        'name'          => 'SRP Administration',
        'icon'          => 'fa-rocket',
        'route_segment' => 'srp',
        'route'         => 'srpadmin.list',
    ],
];
