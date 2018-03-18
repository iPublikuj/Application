<?php
/**
 * Test: IPub\Application\Libraries
 * @testCase
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
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
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
class SecondTranslator implements Localization\ITranslator
{
	/**
	 * Implement nette smart magic
	 */
	use Nette\SmartObject;

	/**
	 * {@inheritdoc}
	 */
	public function translate($message, $count = NULL) : string
	{
		return (string) $message;
	}
}
