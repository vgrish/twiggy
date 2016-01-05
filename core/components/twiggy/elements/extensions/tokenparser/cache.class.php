<?php

class Cache extends Twig_TokenParser
{

	/**
	 * @param Twig_Token $token
	 *
	 * @return bool
	 */
	public function decideCacheEnd(Twig_Token $token)
	{
		return $token->test('endcache');
	}

	/**
	 * @return string
	 */
	public function getTag()
	{
		return 'cache';
	}

	/**
	 * @param Twig_Token $token
	 *
	 * @return CacheNode
	 * @throws Twig_Error_Syntax
	 */
	public function parse(Twig_Token $token)
	{
		$lineno = $token->getLine();
		$stream = $this->parser->getStream();
		$annotation = $this->parser->getExpressionParser()->parseExpression();
		$key = $this->parser->getExpressionParser()->parseExpression();
		$stream->expect(Twig_Token::BLOCK_END_TYPE);
		$body = $this->parser->subparse(array($this, 'decideCacheEnd'), true);
		$stream->expect(Twig_Token::BLOCK_END_TYPE);

		return new CacheNode($annotation, $key, $body, $lineno, $this->getTag());
	}
}