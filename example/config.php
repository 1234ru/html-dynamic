<?php return 
[
    'websun' => __DIR__ . '/../_lib/websun.php', // путь к файлу https://webew.ru/articles/3609.webew

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
                'some_field' => [
                    'title' => 'Множественный чекбокс',
                    'multi_check' => true,
                    'values' => [
                        'one' => 'раз',
                        'two' => 'два',
                        'three' => 'три'
                    ]
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