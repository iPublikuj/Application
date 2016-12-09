<?php
/**
 * TEntityCall.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:Application!
 * @subpackage	UI
 * @since		5.0
 *
 * @date		14.11.14
 */

namespace IPub\Application\UI;

use Nette;
use Nette\Application;
use Nette\Application\UI;

/**
 * Entity call implementation into presenters and controls
 *
 * @package		iPublikuj:Application!
 * @subpackage	UI
 *
 * @method UI\ComponentReflection getReflection()
 * @method void checkRequirements(Nette\Reflection\Method $element)
 */
trait TEntityCall
{
	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $callEntityManager;

	/**
	 * @param \Doctrine\ORM\EntityManager $callEntityManager
	 */
	public function injectCallEntityManager(\Doctrine\ORM\EntityManager $callEntityManager)
	{
		$this->callEntityManager = $callEntityManager;
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
						if ($class = $param->getClass()) {
							$className = $class->getName();
							$paramName = $param->getName();

							if ($paramValue = $args[$i]) {
								if ($paramValue instanceof $className) {
									$entity = $paramValue;

								} else {
									$entity = $this->findCallEntityById($className, $paramValue);
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
	 * @param mixed $id
	 *
	 * @return object|null
	 */
	protected function findCallEntityById($entityName, $id)
	{
		return $this->callEntityManager->find($entityName, $id);
	}
}
