<?php
	/**
	 * Created by PhpStorm.
	 * User: horacekp
	 * Date: 24/02/16
	 * Time: 23:36
	 */

	namespace Wunderman\CMS\Feeds\Parsers;

	interface IParser
	{
		/**
		 * @return array <code>[ [
		 *      'datetime' => \DateTime,
		 *      'header' => 'string',
		 *      'perex' => 'string',
		 *      'detail' => 'string'
		 * ] ]</code>
		 */
		public function parse();

		/**
		 * Set up parser parameters.
		 *
		 * @param array $options
		 * @return $this
		 */
		public function setUp(array $options);
	}