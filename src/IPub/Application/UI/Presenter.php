<?php
/**
 * Presenter.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:Application!
 * @subpackage	UI
 * @since		5.0
 *
 * @date		12.03.14
 */

namespace IPub\Application\UI;

use Nette;
use Nette\Application;
use Nette\Http;
use Nette\Localization;

abstract class Presenter extends Application\UI\Presenter
{
	/**
	 * @var string|null
	 */
	protected $backlink;

	/**
	 * @var Http\Session
	 */
	protected $session;

	/**
	 * @var Localization\ITranslator
	 */
	protected $translator;

	/**
	 * @var Http\IRequest
	 */
	protected $httpRequest;

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $entityManager;

	/**
	 * @param Http\Session $session
	 * @param Http\IRequest $httpRequest
	 */
	public function injectHttp(Http\Session $session, Http\IRequest $httpRequest)
	{
		$this->session		= $session;
		$this->httpRequest	= $httpRequest;
	}

	/**
	 * @param \Doctrine\ORM\EntityManager $entityManager
	 */
	public function injectDoctrineEM(\Doctrine\ORM\EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
	}

	/**
	 * @param Localization\ITranslator $translator
	 */
	public function injectTranslator(Localization\ITranslator $translator)
	{
		$this->translator = $translator;
	}

	/**
	 * Get app translator service
	 *
	 * @return Localization\ITranslator
	 */
	public function getTranslator()
	{
		return $this->translator;
	}

	/**
	 * Redirect only if not ajax
	 *
	 * @param string $destination
	 * @param array $args
	 * @param array $snippets
	 */
	final public function go($destination, $args = [], $snippets = [])
	{
		if ($this->isAjax()) {
			if ($destination === 'this') {
				foreach($snippets as $snippet) {
					$this->redrawControl($snippet);
				}

			} else {
				$this->forward($destination, $args);
			}

		} else {
			$this->redirect($destination, $args);
		}
	}

	/**
	 * @param string $name
	 * @param null $default
	 *
	 * @return mixed
	 */
	protected function getContextParameter($name, $default = NULL)
	{
		$params = $this->context->getParameters();

		return (isset($params[$name])) ? $params[$name] : $default;
	}

	/**
	 * Add translator to flash messages
	 *
	 * @param string $message
	 * @param string $type
	 *
	 * @return string
	 */
	public function flashMessage($message, $type = "info")
	{
		if (is_array($message)) {
			if (isset($message['message'])) {
				// Parse values from array
				$count		= isset($message['count']) ? $message['count'] : NULL;
				$parameters	= isset($message['parameters']) ? $message['parameters'] : [];
				$message	= $message['message'];

				$message = $this->translator->translate($message, $count, $parameters);

			} else {
				return FALSE;
			}

		} else {
			$message = $this->translator->translate($message);
		}

		return parent::flashMessage($message, $type);
	}

	/**
	 * @return string
	 */
	protected function getBaseUrl()
	{
		return $this->getHttpRequest()->url->baseUrl;
	}

	/**
	 * Calls public method if exists
	 *
	 * @param string $method
	 * @param array $params
	 *
	 * @return bool does method exist?
	 *
	 * @throws Application\BadRequestException
	 */
	public function tryCall($method, array $params)
	{
		$rc = $this->getReflection();

		if ($rc->hasMethod($method)) {
			$rm = $rc->getMethod($method);

			if ($rm->isPublic() && !$rm->isAbstract() && !$rm->isStatic()) {
				$this->checkRequirements($rm);
				$args = $rc->combineArgs($rm, $params);

				if (Nette\Utils\Strings::match($method, "~^(action|render|handle).+~")) {
					$methodParams = $rm->getParameters();

					foreach ($methodParams as $i => $param) {
						/**
						 * @var Nette\Reflection\Parameter $param
						 */
						if ($className = $param->getClassName()) {
							$paramName = $param->getName();

							if ($paramValue = $args[$i]) {
								if ($paramValue instanceof $className) {
									$entity = $paramValue;

								} else {
									$entity = $this->findById($className, $paramValue);
								}

								if ($entity) {
									$args[$i] = $entity;

								} else {
									throw new Application\BadRequestException("Value '$paramValue' not found in collection '$className'.");
								}

							} else {
								if (!$param->allowsNull()) {
									throw new Application\BadRequestException("Value '$param' cannot be NULL.");
								}
							}
						}
					}
				}

				$rm->invokeArgs($this, $args);

				return TRUE;
			}
		}

		return FALSE;
	}

	/**
	 * Find entity by ID
	 *
	 * @param string $entityName
	 * @param int $id
	 *
	 * @return object|null
	 */
	protected function findById($entityName, $id)
	{
		return $this->entityManager->find($entityName, $id);
	}
}