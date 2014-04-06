<?php
/**
 * JsonPresenter.php
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

use IPub\Application\Responses;

abstract class JsonPresenter extends Presenter
{
	/**
	 * @var bool
	 */
	protected $compileVariables = FALSE;

	/**
	 * @var Responses\JSONResponse
	 */
	protected $response;

	/**
	 * @return Responses\JSONResponse
	 */
	protected function createResponse()
	{
		return new Responses\JSONResponse($this->compileVariables);
	}

	/**
	 * @return Responses\JSONResponse
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
		throw new Nette\InvalidStateException("Json presenter does not support access to \$template use \$response instead.");
	}

	public function sendTemplate()
	{
		$this->sendResponse($this->getResponse());
	}
}