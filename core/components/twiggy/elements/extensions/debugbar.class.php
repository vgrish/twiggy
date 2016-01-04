<?php

require_once dirname(dirname(dirname(__FILE__))) . '/vendor/Debugbar/vendor/autoload.php';

class Twig_Extensions_Extension_DebugBar extends Twig_Extension
{
	/** @var $debugbar */
	static private $debugbar;
	/** @var $renderer */
	static private $renderer;

	/**
	 * @param Twiggy $Twiggy
	 */
	public function __construct(Twiggy &$Twiggy)
	{
		self::$debugbar = new \DebugBar\StandardDebugBar();
		self::$renderer = self::$debugbar->getJavascriptRenderer();

		$assetPath = MODX_ASSETS_URL . 'components/twiggy/vendor/Debugbar/src/DebugBar/Resources/';
		self::$renderer->setBaseUrl($assetPath);
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'twiggy/DebugBar';
	}

	/**
	 * @return array
	 */
	public function getFunctions()
	{
		return array(
			new Twig_SimpleFunction('dbgHead', 'Twig_Extensions_Extension_DebugBar::renderHead'),
			new Twig_SimpleFunction('dbgMessage', 'Twig_Extensions_Extension_DebugBar::addMessage'),
			new Twig_SimpleFunction('dbgRender', 'Twig_Extensions_Extension_DebugBar::render'),
		);

	}

	/**
	 * @return string
	 */
	public static function renderHead()
	{
		return self::$renderer->renderHead();
	}

	/**
	 * @param        $text
	 * @param string $label
	 */
	public static function addMessage($text, $label = 'info')
	{
		self::$debugbar['messages']->addMessage($text, $label);
	}

	/**
	 * @return string
	 */
	public static function render($initialize = true, $renderStackedData = true)
	{
		return self::$renderer->render($initialize, $renderStackedData);
	}

}