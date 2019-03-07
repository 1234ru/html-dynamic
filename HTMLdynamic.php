<?php

require_once 'websun.php';

class HTMLdynamic {

	private $config;
	private $baseDir; // все каталоги - с закрывающим слэшом
	private $templatesDir;
	private $pagesDir;
	
	function __construct($config) {
		$this->config = $config;
		$this->baseDir = dirname(debug_backtrace()[0]['file']) . '/';
		$this->templatesDir = dirname($this->baseDir . $this->config['template']);
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
			
			$dir = isset($cfg['dir']) ? $cfg['dir'] . '/' : '';
			
			$page['page'] = [ 
				'title' => $cfg['title'],
				'css' => $this->listPageClientFiles('css', $cfg),
				'js' => $this->listPageClientFiles('js', $cfg),
			];
			
			$page['variants'] = $_GET['v'] ?? [];
			
			if (isset($cfg['content']))
				$page['content'] = $this->pagesDir . $dir . $cfg['content']; 
			
			if (isset($cfg['data'])) 
				$page['data'] = is_string($cfg['data'])
					? require $this->pagesDir . $dir . $cfg['data']
					: $cfg['data'] ;
			
			$html = websun_parse_template_path(
				$page,
				basename($this->config['template']), // путь к каталогу откидываем,
				$this->templatesDir // т.к. он уже учтён в templatesDir 
			);
			
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
	 */
	function listPageClientFiles($type, $page_cfg = []) {
		$list = [];
		if (isset($this->config[$type]))
			$list = is_array($this->config[$type])
				? $this->config[$type] 
				: [ $this->config[$type] ] ;
		if (isset($page_cfg[$type])) {
			$list = array_merge(
				$list,
				array_map( // подставляем каталог страницы к адресу файла 
					function($path) use ($page_cfg) {
						if ( substr($path, 0, 2) == '$/' ) // путь относительно корневого каталога инсталляции - просто удаляем маркер
							$path = substr($path, 2);
						elseif ( substr($path, 0, 1) != '/' AND ($page_cfg['dir'] ?? '') ) // путь относительно каталога страницы
							$path = "pages/$page_cfg[dir]/$path";
						else // абсолютный путь оставляем без изменений
							; 
						return $path;	
					},
					is_array($page_cfg[$type]) ? $page_cfg[$type] : [ $page_cfg[$type] ]
				)
			);
		}
		elseif ($type == 'css' AND isset($page_cfg['dir'])) {
			foreach (scandir($this->pagesDir . $page_cfg['dir']) as $file) {
				if ( pathinfo($file, PATHINFO_EXTENSION) == $type )
					$list[] = "pages/$page_cfg[dir]/$file";
			}
		}
		
		return $list;
	}
	
	/** Генерирует HTML-код страницы оглавления.
	 * @return string
	 */
	function generateIndexHTML() {
		
		$webdir = substr(__DIR__, strlen($_SERVER['DOCUMENT_ROOT']) ) . '/index/';
		
		$index = [
			'page' => [
				'title' => 'Список макетов',
				'js' => [ $webdir . 'jquery.min.js', $webdir . 'ready.js' ],
				'css' => [ $webdir . 'style.css' ],
			],
			'list' => $this->config['pages']
		];
		
		$html = websun_parse_template_path(
			$index,
			__DIR__ . '/index/page.tpl',
			$this->templatesDir
		);
		
		return $html;
	}
}