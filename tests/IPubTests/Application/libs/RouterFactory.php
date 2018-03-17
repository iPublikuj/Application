<?php
/**
 * Test: IPub\Application\Libraries
 * @testCase
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec https://www.ipublikuj.eu
 * @package        iPublikuj:Application!
 * @subpackage     Tests
 * @since          1.1.0
 *
 * @date           10.12.16
 */

declare(strict_types = 1);

namespace IPubTests\Application\Libs;

use Nette\Application;
use Nette\Application\Routers;

/**
 * Simple routes factory
 *
 * @package        iPublikuj:Application!
 * @subpackage     Tests
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
class RouterFactory
{
	/**
	 * @return Application\IRouter
	 */
	public static function createRouter() : ?Application\IRouter
	{
		$router = new Routers\RouteList();
		$router[] = new Routers\Route('<presenter>/<action>[/<id>]', 'Test:default');

		return $router;
	}
}
