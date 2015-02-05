<?php
/**
 * TRestPresenter.php
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
use Nette\Utils;

use Tracy\Debugger;

use IPub\Application;

/**
 * REST API presenter
 *
 * @package		iPublikuj:Application!
 * @subpackage	UI
 *
 * @method Nette\Http\IRequest getHttpRequest()
 *
 * @property Application\Responses\JsonResponse $response
 */
trait TRestPresenter
{
	use TJsonPresenter;

	/**
	 * @var array
	 */
	protected $requestData;

	/**
	 * @return void
	 */
	protected function startup()
	{
		parent::startup();

		$this->requestData = $this->getHttpRequest()->getPost();

		try {
			$this->checkMethodRequest();

		} catch (Nette\InvalidStateException $ex) {
			$this->returnException($ex);
		}
	}
	
	/**
	 * @param \Exception $ex
	 *
	 * @return string
	 */
	protected function returnException(\Exception $ex)
	{
		Debugger::log($ex);

		$this->response->status		= 'error';
		$this->response->message	= $ex->getMessage();

		$this->sendResponse($this->getResponse());
	}

	/**
	 * @param $data
	 *
	 * @return string
	 */
	protected function returnResponse($data = array())
	{
		$this->response->status	= 'success';
		$this->response->data	= $data;
	}

	/**
	 * @throw Nette\InvalidStateException
	 */
	protected function checkMethodRequest()
	{
		$methodName = 'action' . Utils\Strings::firstUpper($this->action);
		$rc = $this->getReflection();

		if ($rc->hasMethod($methodName) && $method = $rc->getMethod($methodName)) {
			if ($method->hasAnnotation('method') && $annotation = $method->getAnnotation('method')) {
				$request = $this->getHttpRequest();

				if (Utils\Strings::lower($annotation) !== Utils\Strings::lower($request->getMethod())) {
					throw new Nette\InvalidStateException('Bad method for this request. ' . __CLASS__ . '::' . $methodName);
				}
			}
		}
	}
}