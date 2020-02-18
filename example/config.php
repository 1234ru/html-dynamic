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
				'has_discount' => 'Есть скидка',
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

    'data' => [
        // тут общие данные для всех страниц
        // могут дополняться в частных конфигах,
        // а также вообще отсутствовать
        // в шаблонах обращение через {*data.key*}
    ]
];