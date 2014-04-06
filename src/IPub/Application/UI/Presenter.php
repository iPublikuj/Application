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
use Nette\Application\BadRequestException;

abstract class Presenter extends Nette\Application\UI\Presenter
{
	/**
	 * Implements autowiring extension
	 */
	use \Kdyby\Autowired\AutowireProperties;
	use \Kdyby\Autowired\AutowireComponentFactories;

	/**
	 * Implement images extension
	 */
	use \IPub\Images\TImagePipe;

	/**
	 * Implement gravatar extension
	 */
	use \IPub\Gravatar\TGravatar;

	/**
	 * @autowire
	 * @var \Nette\Http\Session
	 */
	protected $session;

	/**
	 * @autowire
	 * @var \Kdyby\Translation\Translator
	 */
	protected $translator;

	/**
	 * @autowire
	 * @var \Nette\Http\IRequest
	 */
	protected $httpRequest;

	/**
	 * @autowire
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $entityManager;

	/**
	 * @var string|null
	 */
	protected $backlink;

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
		$message = $this->translator->translate($message);

		return parent::flashMessage($message, $type);
	}

	/**
	 * Get app translator service
	 *
	 * @return type
	 */
	public function getTranslator()
	{
		return $this->translator;
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
	 * @throws BadRequestException
	 */
	protected function tryCall($method, array $params)
	{
		$rc = $this->getReflection();

		if ($rc->hasMethod($method)) {
			$rm = $rc->getMethod($method);

			if ($rm->isPublic() && !$rm->isAbstract() && !$rm->isStatic()) {
				$this->checkRequirements($rm);
				$args = $rc->combineArgs($rm, $params);

				if (\Nette\Utils\Strings::match($method, "~^(action|render|handle).+~")) {
					$methodParams = $rm->getParameters();

					foreach ($methodParams as $i => $param) {
						/**
						 * @var \Nette\Reflection\Parameter $param
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
									throw new BadRequestException("Value '$paramValue' not found in collection '$className'.");
								}

							} else {
								if (!$param->allowsNull()) {
									throw new BadRequestException("Value '$param' cannot be NULL.");
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