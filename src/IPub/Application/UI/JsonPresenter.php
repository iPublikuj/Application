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

<<<<<<< HEAD
use Nette;

use IPub\Application\Responses;

=======
>>>>>>> 95195fa21e6734a5aaa443f2e0ef02e20f86b24e
abstract class JsonPresenter extends Presenter
{
	/**
	 * @var bool
	 */
	protected $compileVariables = FALSE;

	/**
<<<<<<< HEAD
	 * @var Responses\JSONResponse
=======
	 * @var \IPub\Application\Responses\Json
>>>>>>> 95195fa21e6734a5aaa443f2e0ef02e20f86b24e
	 */
	protected $response;

	/**
<<<<<<< HEAD
	 * @return Responses\JSONResponse
	 */
	protected function createResponse()
	{
		return new Responses\JSONResponse($this->compileVariables);
	}

	/**
	 * @return Responses\JSONResponse
=======
	 * @return \IPub\Application\Responses\Json
	 */
	protected function createResponse()
	{
		return new \IPub\Application\Responses\Json($this->compileVariables);
	}

	/**
	 * @return \IPub\Application\Responses\Json
>>>>>>> 95195fa21e6734a5aaa443f2e0ef02e20f86b24e
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
<<<<<<< HEAD
	 * @throws Nette\InvalidStateException
	 */
	protected function createTemplate($class = NULL)
	{
		throw new Nette\InvalidStateException("Json presenter does not support access to \$template use \$response instead.");
=======
	 * @throws \Nette\InvalidStateException
	 */
	protected function createTemplate($class = NULL)
	{
		throw new \Nette\InvalidStateException("Json presenter does not support access to \$template use \$response instead.");
>>>>>>> 95195fa21e6734a5aaa443f2e0ef02e20f86b24e
	}

	public function sendTemplate()
	{
		$this->sendResponse($this->getResponse());
	}
}