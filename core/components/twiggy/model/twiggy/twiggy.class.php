<?php

// For debug
ini_set('display_errors', 1);
ini_set('error_reporting', -1);


/**
 * The base class for Twiggy.
 */
class Twiggy
{
	/* @var modX $modx */
	public $modx;
	/** @var Twig $twig */
	public $twig;
	/** @var twiggyParser $parser */
	public $parser;
	/** @var array $config */
	public $config = array();
	/** @var string $namespace */
	protected $namespace = 'twiggy';

	protected $debug;

	/**
	 * @param modX  $modx
	 * @param array $config
	 */
	function __construct(modX &$modx, array $config = array())
	{
		$this->modx =& $modx;
		$this->setConfig(array_merge($this->getConfig(), $config));
	}

	/**
	 * @param       $key
	 * @param array $config
	 * @param null  $default
	 *
	 * @return mixed|null
	 */
	public function getOption($key, $config = array(), $default = null, $skipEmpty = false)
	{
		$option = $default;
		if (!empty($key) AND is_string($key)) {
			if ($config != null AND array_key_exists($key, $config)) {
				$option = $config[$key];
			} elseif (array_key_exists($key, $this->config)) {
				$option = $this->config[$key];
			} elseif (array_key_exists("{$this->namespace}_{$key}", $this->modx->config)) {
				$option = $this->modx->getOption("{$this->namespace}_{$key}");
			}
		}
		if ($skipEmpty AND empty($option)) {
			$option = $default;
		}

		return $option;
	}

	/**
	 * @return mixed
	 */
	public function getConfig()
	{
		/* array cache $options */
		$options = array(
			'cache_key' => 'config/123456',
			'cacheTime' => 0,
		);
		if (!$config = $this->getCache($options)) {
			$q = $this->modx->newQuery('modSystemSetting', array('area' => 'twiggy_twig'));
			$q->select('key');
			if ($q->prepare() && $q->stmt->execute()) {
				$settings = $q->stmt->fetchAll(PDO::FETCH_COLUMN);
				foreach ($settings as $setting) {
					$config[str_replace('twiggy_', '', $setting)] = $this->modx->getOption($setting, null);
				}
			}
			$this->setCache($config, $options);
		}

		return $config;
	}

	/**
	 * @param array $config
	 */
	public function setConfig(array $config = array())
	{
		$this->config = array_merge(array(
			'debug'      => false,
			'cache'      => null,
			'template'   => null,
			'autoescape' => false,
		), $config);
	}

	/**
	 * @return array
	 */
	public function getDataBaseTemplates()
	{
		/* array cache $options */
		$options = array(
			'cache_key' => 'config/templates/database',
			'cacheTime' => 0,
		);
		if (!$templates = $this->getCache($options)) {
			$q = $this->modx->newQuery('modTemplate');
			$q->select('templatename,content');
			if ($q->prepare() && $q->stmt->execute()) {
				$rows = $q->stmt->fetchAll(PDO::FETCH_ASSOC);
				foreach ($rows as $row) {
					$templates[$row['templatename']] = $row['content'];
				}
			}
			$this->setCache($templates, $options);
		}

		return (array)$templates;
	}

	/**
	 * @return array
	 */
	public function getFileTemplates()
	{
		/* array cache $options */
		$options = array(
			'cache_key' => 'config/templates/file',
			'cacheTime' => 0,
		);
		if (!$templates = $this->getCache($options)) {
			$paths = $this->explodeAndClean($this->getOption('path_templates', null, '', true));
			foreach ($paths as $path) {
				$files = scandir($path);
				foreach ($files as $file) {
					if (preg_match('/.*?\.tpl$/i', $file)) {
						$templates[str_replace('.tpl', '', $file)] = file_get_contents($path . '/' . $file);
					}
				}
			}
			$this->setCache($templates, $options);
		}

		return (array)$templates;
	}

	/**
	 * @return twiggyParser
	 */
	protected function getParser()
	{
		$this->parser = $this->modx->getParser();
		if (!($this->parser instanceof twiggyParser)) {
			if (!class_exists('twiggyParser')) {
				require_once dirname(__FILE__) . '/twiggyparser.class.php';
			}
			$this->parser = new twiggyParser($this->modx);
		}

		return $this->parser;
	}

	/**
	 * @return bool|Twig|Twig_Environment
	 */
	public function getTwig()
	{

		if (!$this->twig) {
			try {
				require_once dirname(dirname(dirname(__FILE__))) . '/vendor/Twig/vendor/autoload.php';

				/* get $templates */
				$templates = array_merge(
					$this->getFileTemplates(),
					$this->getDataBaseTemplates()
				);

				$twig = new Twig_Environment(new Twig_Loader_Array($templates), $this->config);
				$this->debug = (boolean)$this->getOption('debug', $this->config, false, true);
				if ($this->debug) {
					$twig->addExtension(new Twig_Extension_Debug());
				}

				/** load $extensions */
				$extensions = $this->explodeAndClean($this->getOption('extensions', $this->config, '', true));
				if (!empty($extensions)) {
					$pathExtensions = trim($this->getOption('path_extensions', $this->config, '', true));
					$this->loadExtensions($pathExtensions);
					foreach ($extensions as $extension) {
						$extension = 'Twig_Extensions_Extension_' . $extension;
						${$extension} = new $extension($this);
						if (class_exists($extension) AND ${$extension} instanceof Twig_Extension) {
							$twig->addExtension(${$extension});
						}
					}
				}

				$twig->addGlobal('modx', $this->modx);
				$this->twig = &$twig;
			} catch (Exception $e) {
				$this->modx->log(xPDO::LOG_LEVEL_ERROR, $e->getMessage());

				return false;
			}
		}

		return $this->twig;
	}

	/**
	 * @param $path
	 */
	public function loadExtensions($path)
	{
		$files = scandir($path);
		foreach ($files as $file) {
			if (preg_match('/.*?\.class\.php$/i', $file)) {
				include_once($path . '/' . $file);
			}
		}
	}

	/**
	 * @param        $array
	 * @param string $delimiter
	 *
	 * @return array
	 */
	public function explodeAndClean($array, $delimiter = ',')
	{
		$array = explode($delimiter, $array);     // Explode fields to array
		$array = array_map('trim', $array);       // Trim array's values
		$array = array_keys(array_flip($array));  // Remove duplicate fields
		$array = array_filter($array);            // Remove empty values from array
		return $array;
	}

	/**
	 * @param        $array
	 * @param string $delimiter
	 *
	 * @return array|string
	 */
	public function cleanAndImplode($array, $delimiter = ',')
	{
		$array = array_map('trim', $array);       // Trim array's values
		$array = array_keys(array_flip($array));  // Remove duplicate fields
		$array = array_filter($array);            // Remove empty values from array
		$array = implode($delimiter, $array);

		return $array;
	}

	/**
	 * @param       $content
	 * @param array $properties
	 *
	 * @return string
	 */
	public function process($content, array $properties = array())
	{
		$content = is_array($content) ? trim($content['content']) : trim($content);
		if (!preg_match('#\{.*\}#', $content)) {
			return $content;
		}
		if (!$twig = $this->getTwig()) {
			return $content;
		}
		try {
			$template = $twig->createTemplate($content);
			$content = $template->render($properties);

		} catch (Exception $e) {
			$this->modx->log(modX::LOG_LEVEL_ERROR, $e->getMessage());
		}

		return $content;
	}

	/**
	 * Sets data to cache
	 *
	 * @param mixed $data
	 * @param mixed $options
	 *
	 * @return string $cacheKey
	 */
	public function setCache($data = array(), $options = array())
	{
		$cacheKey = $this->getCacheKey($options);
		$cacheOptions = $this->getCacheOptions($options);
		if (!empty($cacheKey) AND !empty($cacheOptions) AND $this->modx->getCacheManager()) {
			$this->modx->cacheManager->set(
				$cacheKey,
				$data,
				$cacheOptions[xPDO::OPT_CACHE_EXPIRES],
				$cacheOptions
			);
		}

		return $cacheKey;
	}

	/**
	 * Returns data from cache
	 *
	 * @param mixed $options
	 *
	 * @return mixed
	 */
	public function getCache($options = array())
	{
		$cacheKey = $this->getCacheKey($options);
		$cacheOptions = $this->getCacheOptions($options);
		$cached = '';
		if (!empty($cacheOptions) AND !empty($cacheKey) AND $this->modx->getCacheManager()) {
			$cached = $this->modx->cacheManager->get($cacheKey, $cacheOptions);
		}

		return $cached;
	}

	/**
	 * @param array $options
	 *
	 * @return bool
	 */
	public function clearCache($options = array())
	{
		$cacheKey = $this->getCacheKey($options);
		$cacheOptions = $this->getCacheOptions($options);
		if (!empty($cacheKey)) {
			$cacheOptions['cache_key'] .= $cacheKey;
		}
		if (!empty($cacheOptions) AND $this->modx->getCacheManager()) {
			return $this->modx->cacheManager->clean($cacheOptions);
		}

		return false;
	}


	/**
	 * Returns array with options for cache
	 *
	 * @param $options
	 *
	 * @return array
	 */
	protected function getCacheOptions($options = array())
	{
		if (empty($options)) {
			$options = $this->config;
		}
		$cacheOptions = array(
			xPDO::OPT_CACHE_KEY     => empty($options['cache_key'])
				? 'default'
				: 'default/' . $this->namespace . '/',
			xPDO::OPT_CACHE_HANDLER => !empty($options['cache_handler'])
				? $options['cache_handler']
				: $this->modx->getOption('cache_resource_handler', null, 'xPDOFileCache'),
			xPDO::OPT_CACHE_EXPIRES => (isset($options['cacheTime']) AND $options['cacheTime'] !== '')
				? (integer)$options['cacheTime']
				: (integer)$this->modx->getOption('cache_resource_expires', null, 0),
		);

		return $cacheOptions;
	}

	/**
	 * @return bool
	 */
	public function clearTwiggyCache()
	{
		$folder = rtrim(trim($this->getOption('cache', $this->config, MODX_CORE_PATH . 'cache/default/twiggy/', true)), 'cache/') . '/';
		return $this->modx->cacheManager->deleteTree($folder, array('deleteTop' => true, 'extensions' => array('.string.php', '.cache.php', '.php')));
	}

	/**
	 * Returns key for cache of specified options
	 *
	 * @var mixed $options
	 *
	 * @return bool|string
	 */
	public function getCacheKey($options = array())
	{
		if (empty($options)) {
			$options = $this->config;
		}
		if (!empty($options['cache_key'])) {
			return $options['cache_key'];
		}
		$key = !empty($this->modx->resource)
			? $this->modx->resource->getCacheKey()
			: '';

		return $key . '/' . sha1(serialize($options));
	}

}