<?php
/**
 * TJsonPresenter.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:Application!
 * @subpackage	UI
 * @since		5.0
 *
 * @date		10.02.13
 */

namespace IPub\Application\UI;

use Nette;

use IPub\Application;

/**
 * JSON presenter trait
 *
 * @package		iPublikuj:Application!
 * @subpackage	UI
 *
 * @method sendResponse(Nette\Application\IResponse $response)
 */
trait TJsonPresenter
{
	/**
	 * @var bool
	 */
	protected $compileVariables = FALSE;

	/**
	 * @var Application\Responses\JsonResponse
	 */
	protected $response;

	/**
	 * @return Application\Responses\JsonResponse
	 */
	protected function createResponse()
	{
		return new Application\Responses\JsonResponse($this->compileVariables);
	}

	/**
	 * @return Application\Responses\JsonResponse
	 */
	public function getResponse()
	{
		if (!$this->response) {
			$this->response = $this->createResponse();
		}

		return $this->response;
	}

	/**
	 * @param string
	 *
	 * @return void
	 *
	 * @throws Nette\InvalidStateException
	 */
	protected function createTemplate($class = NULL)
	{
		throw new Nette\InvalidStateException("JSON presenter does not support access to \$template use \$response instead.");
	}

	/**
	 * Sends response and terminates presenter
	 *
	 * @return void
	 *
	 * @throws Nette\Application\AbortException
	 */
	public function sendTemplate()
	{
		$this->sendResponse($this->getResponse());
	}
}