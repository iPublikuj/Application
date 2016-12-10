<?php
/**
 * Test: IPub\Application\Libraries
 * @testCase
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:Application!
 * @subpackage     Tests
 * @since          1.1.0
 *
 * @date           10.12.16
 */

declare(strict_types = 1);

namespace IPubTests\Application\Libs;

use Nette;
use Nette\Localization;

/**
 * Dummy translator service for testing
 *
 * @package        iPublikuj:Application!
 * @subpackage     Tests
 */
class Translator extends Nette\Object implements Localization\ITranslator
{
	/**
	 * {@inheritdoc}
	 */
	public function translate($message, $count = NULL) : string
	{
		return (string) $message;
	}
}
