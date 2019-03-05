<?php return 
[

	'template' => 'templates/_main.tpl',
	
	'js' => '/js/jquery.min.js',

	'pages' => [
		1 => [ // с явно указанным индексом - чтобы нумерация начиналась с единицы
			'title' => 'Главная страница',
			'dir' => '_mainpage',
			'content' => 'content.tpl',
		],
		
		[
			'title' => 'Страница товара',
			'dir' => 'product',
			'content' => 'content.tpl',
			'variants' => [
				'has_discount' => [
					'values' => [
						1 => 'Есть скидка',
					],
				],
				'availability' => [
					'title' => 'Наличие',
					'values' => [
						'POD_ZAKAZ' => 'под заказ',
						'NO' => 'нет в наличии',
					],
				],
			],
		],
	],
];