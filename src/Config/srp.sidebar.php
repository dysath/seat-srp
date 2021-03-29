<?PHP 

return [
	'srp' => [
		'name' => 'Ship Replacement Program',
		'icon' => 'fas fa-rocket',
		'route_segment' => 'srp',
		'permission' => 'srp.request',
		'entries' => [
			[
				'name' => 'Request',
				'icon' => 'fas fa-medkit',
				'route' => 'srp.request',
				'permission' => 'srp.request',
			],
			[
				'name' => 'Approval',
				'icon' => 'fas fa-gavel',
				'route' => 'srpadmin.list',
				'permission' => 'srp.settle',
			],
            [
                'name' => 'Metrics',
                'icon' => 'fas fa-chart-bar',
                'route' => 'srp.metrics',
                'permission' => 'srp.settle',
			],
			[
                'name' => 'Settings',
                'icon' => 'fas fa-cogs',
                'route' => 'srp.settings',
                'permission' => 'srp.settings',
			],
			[
                'name' => 'Instructions',
                'icon' => 'fas fa-book-open',
                'route' => 'srp.instructions',
                'permission' => 'srp.request',
			],
			[
                'name' => 'About',
                'icon' => 'fas fa-info',
                'route' => 'srp.about',
                'permission' => 'srp.request',
            ],
		],
	],
];
