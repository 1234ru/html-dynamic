<?php

namespace One234ru;

class HTMLdynamic {

	private $config;
	private $baseDir; // все каталоги - с закрывающим слэшом
    private $webDir;
	private $templatesDir;
	private $pagesDir;

	function __construct($config) {
		$this->config = self::expandBriefVariants($config);
        $this->baseDir = dirname(debug_backtrace()[0]['file']) . '/';
        $this->webDir = self::webPath($this->baseDir);
		$this->templatesDir = $this->determineTemplatesDir();
		$this->pagesDir = $this->baseDir . 'pages/';
	}

	/** Генерирует HTML-код страницы.
	 * @param array $params сюда передавать массив $_GET
	 * @return string
	 */
	function generate($params) {
		
		if (!isset($params['page'])) {
			$html = $this->generateIndexHTML();
		}
		elseif ( $cfg = ($this->config['pages'][ $params['page'] ] ?? FALSE) ) {
			
			// $dir = isset($cfg['dir']) ? $cfg['dir'] . '/' : '';
			$dir = self::getPageDirPath($cfg, true);

			$page['page'] = [ 
				'title' => $cfg['title'],
				'css' => $this->listPageClientFiles('css', $cfg),
				'js' => $this->listPageClientFiles('js', $cfg),
			];
			
			$page['variants'] = $_GET['v'] ?? [];

			if (!isset($cfg['content'])) {
				$default_content_file = 'content.tpl';
				if (is_file($this->filePathOfPage($dir, $default_content_file))) {
                    $cfg['content'] = $default_content_file;
                }
            }
			if (isset($cfg['content'])) {
                $page['content'] = $this->filePathOfPage($dir, $cfg['content']);
            }
            if (!isset($cfg['data'])) {
				$default_data_file = 'data.php';
				$cfg['data'] =
                    (is_file($this->filePathOfPage($dir, $default_data_file)))
					? $default_data_file
					: [] ;
			}

            // сначала данные из частного конфига, потом - из общего
            if (is_string($cfg['data'])) {
                $data_file_path = $this->filePathOfPage($dir, $cfg['data']);
                $ext = pathinfo($data_file_path, PATHINFO_EXTENSION);
                if ($ext == 'php') {
                    $page['data'] = require $data_file_path;
                } elseif ($ext == 'json') {
                    $page['data'] = json_decode(
                        file_get_contents($data_file_path),
                        true
                    );
                } else {
                    trigger_error(
                        "Data file of unknown extension: $data_file_path",
                        E_USER_WARNING
                    );
                    $page['data'] = [];
                }
            } else {
                $page['data'] = $cfg['data'];
            }

            $page['data'] += ( $this->config['data'] ?? [] );

            $template = $this->config['template'];
            if (!self::isFilePathFinal($template)) {
                // Если указан относительный путь к шаблону,
                // каталог откидываем, т.к. он уже учтён в templatesDir.
                $template = basename($template);
            }

			$html = $this->config['html_generation_code'](
                compact('page', 'template')
                + [ 'templates_dir' => $this->templatesDir ]
            ) ;
		}
		else
			$html = "Page <b>$params[page]</b> doesn't exist.";
			
		return $html;
	}
	
	/** Возвращает список js- или css-файлов для указанной конфигурации.
	 * 
	 * При отсутствии в конфигурации явно указанного ключа сканирует каталог (если указан) на предмет наличия файлов с соотв. расширением (которые подключает, если находит).
	 * 
	 * Если конфигурация не указана, возвращает общеиспользуемый список.
	 * 
	 * @param string $type css или js
	 * @param array|void
	 * @return array
	 */
	function listPageClientFiles($type, $page_cfg = []) {
		$list = $this->config[$type] ?? [];
        if (!is_array($list)) {
            $list = [ $list ];
        }
        $page_dir = self::getPageDirPath($page_cfg);
		if (isset($page_cfg[$type])) {
			$list = array_merge(
				$list,
				array_map( // подставляем каталог страницы к адресу файла 
					function($path) use ($page_dir) {
						if ( substr($path, 0, 2) == '$/' ) {
                            // путь относительно корневого каталога
                            // инсталляции - просто удаляем маркер
                            // $path = substr($path, 2);
                            $path = substr($path, 1);
                        } elseif (
                            substr($path, 0, 1) != '/'
                            AND
                            substr($path, 1, 1) != ':' // Win
                        ) {
                            // путь относительно каталога страницы
                            $path = "$page_dir/$path";
                        } // else ; // абсолютный путь оставляем без изменений
						return $path;
					},
					is_array($page_cfg[$type])
                        ? $page_cfg[$type]
                        : [ $page_cfg[$type] ]
				)
			);
		}
		elseif (in_array($type, ['css', 'js']) AND isset($page_cfg['dir'])) {
			foreach (scandir($page_dir) as $file) {
				if ( pathinfo($file, PATHINFO_EXTENSION) == $type ) {
                    $list[] = "$page_dir/$file";
                }
            }
		}

		$list = array_map( [$this, 'addModificationTime'], $list);

		return $list;
	}

	/** Добавляет к пути файла время последнего изменения.
	 *
	 * Нужно для подавления забора из кэша
	 * предыдущих версий css- и js-файлов
	 * (особенно актуально для смартфонов, где,
	 * несмотря на отсутствие заголовков кэширования,
	 * оно всё равно происходит).
	 *
	 * @param string $filepath путь к файлу относительно общего каталога страниц
	 * @return string он же с добавленной временной меткой
	 */
	function addModificationTime($filepath) {
        if (parse_url($filepath, PHP_URL_SCHEME)) {
            // Пути вида http(s)://..., оставляем без изменений
            return $filepath;
        }
        // Абсолютные пути к js и css всегда указаны относительно каталога веб-севрера
		if (self::isFilePathFinal($filepath)) {
            $absoulte_path = !in_array(pathinfo($filepath, PATHINFO_EXTENSION), ['js', 'css'])
                ? $filepath
                : $_SERVER['DOCUMENT_ROOT'] . $filepath;
        } else {
            $absoulte_path = $this->baseDir . ltrim($filepath, '/');
        }
		$t = filemtime($absoulte_path);
		return "$filepath?t=$t";
	}
	
	/** Генерирует HTML-код страницы оглавления.
	 * @return string
	 */
	function generateIndexHTML() {

        // $index_fs_dir = __DIR__ . '/index/';
        // $index_web_dir = self::webPath($index_fs_dir);
		$index_web_dir = $this->webDir . 'index/';
        // должно работать через символическую ссылку
        // html/index -> vendor/one234ru/html-dynamic/index

		$index_data = [
			'page' => [
				'title' => 'Список макетов',
				'js' => [
                    $index_web_dir . 'jquery.min.js',
                    $index_web_dir . 'ready.js'
                ],
				'css' => [
                    $index_web_dir . 'style.css'
                ],
			],
			'list' => $this->config['pages']
		];
		
		$html = websun_parse_template_path(
			$index_data,
			__DIR__ . '/index/page.tpl',
			$this->templatesDir
		);
		
		return $html;
	}

	/**
	 * Раскрывает сокращённую запись варианта.
	 *
	 * Было:  some_variant => Описание
	 * Стало: some_variant => [ values => [ 1 => Описание ] ]
	 * @param array $config общая конфигурация верхнего уровня
	 * @return array
	 */
	static function expandBriefVariants($config) {
		foreach ($config['pages'] as &$page) {
			if (!isset($page['variants']))
				continue;
			foreach ($page['variants'] as &$v)
				if (is_string($v)) {
					$v = [
						'values' => [
							1 => $v
						]
					];
				}
		}
		return $config;
	}

	/** Строит путь к файлу в каталоге страницы.
	 * @param string $page_dir_final название подкаталога страницы в общем каталоге страниц
	 * @param string $filename имя файла
	 * @return string
	 */
	private function filePathOfPage($page_dir_final, $filename) {
        if (!self::isFilePathFinal($filename)) {
            $path = $page_dir_final;
            if (mb_substr($page_dir_final, -1) != '/') {
                $path .= "/";
            }
            $path .= $filename;
        } else {
            $path = $filename;
        }
        return $path;
	}

    private static function isFilePathFinal($path)
    {
        return in_array(mb_substr($path, 0, 1), ['/', '$', '^'])
            OR (mb_substr($path, 1, 1) == ':');
            // Windows - указан абсолютный путь - вида С:/...
    }

    private function determineTemplatesDir()
    {
        return dirname(
            (!self::isFilePathFinal($this->config['template']) ? $this->baseDir : '')
            . $this->config['template']
        );
    }

    public static function webPath($file_system_dir)
    {
        return mb_substr(
            $file_system_dir,
            mb_strlen($_SERVER['DOCUMENT_ROOT'])
        );
    }

    private static function getPageDirPath(
        $page_cfg,
        $add_trailing_slash = false
    ) {
        $path = self::isFilePathFinal($page_cfg['dir'])
            ? $page_cfg['dir']
            : "pages/$page_cfg[dir]";
        if ($add_trailing_slash) {
            $path .= "/";
        }
        return $path;
    }
}