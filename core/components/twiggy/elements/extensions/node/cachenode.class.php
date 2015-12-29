<?php


class CacheNode extends \Twig_Node
{
	private static $cacheCount = 1;

	/**
	 * @param Twig_Node_Expression $annotation
	 * @param Twig_Node_Expression $keyInfo
	 * @param int                  $body
	 * @param null|string          $lineno
	 * @param null                 $tag
	 */
	public function __construct(\Twig_Node_Expression $annotation, \Twig_Node_Expression $keyInfo, $body, $lineno, $tag = null)
	{
		parent::__construct(array('key_info' => $keyInfo, 'body' => $body, 'annotation' => $annotation), array(), $lineno, $tag);
	}

	/**
	 * {@inheritDoc}
	 */
	public function compile(\Twig_Compiler $compiler)
	{
		$i = self::$cacheCount++;
		$compiler
			->addDebugInfo($this)
			->write("\$twiggyCacheKey" . $i . " = \$this->env->getExtension('twiggy/cache')->getKey(")
			->subcompile($this->getNode('annotation'))
			->raw(", ")
			->subcompile($this->getNode('key_info'))
			->write(");\n")
			->write("\$twiggyCacheBody" . $i . " = \$this->env->getExtension('twiggy/cache')->getCache(\$twiggyCacheKey" . $i . ");\n")
			->write("if (\$twiggyCacheBody" . $i . " == \"\") {\n")
			->indent()
			->write("ob_start();\n")
			->indent()
			->subcompile($this->getNode('body'))
			->outdent()
			->write("\n")
			->write("\$twiggyCacheBody" . $i . " = ob_get_clean();\n")
			->write("\$this->env->getExtension('twiggy/cache')->setCache(\$twiggyCacheBody" . $i . ", \$twiggyCacheKey" . $i . ",")
			->subcompile($this->getNode('key_info'))
			->write(");\n")
			->outdent()
			->write("}\n")
			->write("echo \$twiggyCacheBody" . $i . ";\n");
	}
}