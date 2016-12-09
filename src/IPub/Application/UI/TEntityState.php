<?php
/**
 * TEntityState.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:Application!
 * @subpackage	UI
 * @since		5.0
 *
 * @date		21.05.15
 */

namespace IPub\Application\UI;

use Nette;
use Nette\Application;
use Nette\Application\UI;
use Tracy\Debugger;

/**
 * Entity call implementation into presenters and controls
 *
 * @package		iPublikuj:Application!
 * @subpackage	UI
 *
 * @method UI\ComponentReflection getReflection()
 */
trait TEntityState
{
	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $stateEntityManager;

	/**
	 * @param \Doctrine\ORM\EntityManager $stateEntityManager
	 */
	public function injectStateEntityManager(\Doctrine\ORM\EntityManager $stateEntityManager)
	{
		$this->stateEntityManager = $stateEntityManager;
	}

	/**
	 * Loads state informations
	 *
	 * @param array $params
	 *
	 * @return void
	 *
	 * @throws Application\BadRequestException
	 */
	public function loadState(array $params)
	{
		$reflection = $this->getReflection();

		foreach ($reflection->getPersistentParams() as $name => $meta) {
			if (isset($params[$name])) { // NULLs are ignored
				$type = gettype($meta['def']);

				if (!$reflection->convertType($params[$name], $type)) {
					throw new Application\BadRequestException("Invalid value for persistent parameter '$name' in '{$this->getName()}', expected " . ($type === 'NULL' ? 'scalar' : $type) . ".");
				}

				$this->$name = $params[$name];

				/*
				if ($className = $reflection->getProperty($name)->getAnnotation('var')) {
					if ($paramValue = $params[$name]) {
						if ($paramValue instanceof $className) {
							$entity = $paramValue;

						} else {
							$entity = $this->findStateEntityById($className, $paramValue);
						}

						if ($entity) {
							$this->$name = $entity;

						} else {
							throw new Application\BadRequestException("Value '$paramValue' not found in collection '$className'.");
						}
					}
				}
				*/

			} else {
				$params[$name] = $this->$name;
			}
		}

		$this->params = $params;
	}

	/**
	 * Saves state informations for next request
	 *
	 * @param array $params
	 * @param UI\PresenterComponentReflection (internal, used by Presenter) $reflection
	 *
	 * @return void
	 *
	 * @throws UI\InvalidLinkException
	 */
	public function saveState(array & $params, $reflection = NULL)
	{
		$reflection = $reflection === NULL ? $this->getReflection() : $reflection;

		foreach ($reflection->getPersistentParams() as $name => $meta) {
			if (isset($params[$name])) {
				// injected value

			} else if (array_key_exists($name, $params)) { // NULLs are skipped
				continue;

			} elseif (!isset($meta['since']) || $this instanceof $meta['since']) {
				$params[$name] = $this->$name; // object property value

				/*
				if ($className = $reflection->getProperty($name)->getAnnotation('var')) {
					if ($this->$name instanceof $className) {
						$params[$name] = (string) $this->$name;
					}
				}
				*/

			} else {
				continue; // ignored parameter
			}

			$type = gettype($meta['def']);
			if (!UI\PresenterComponentReflection::convertType($params[$name], $type)) {
				throw new UI\InvalidLinkException(sprintf("Invalid value for persistent parameter '%s' in '%s', expected %s.", $name, $this->getName(), $type === 'NULL' ? 'scalar' : $type));
			}

			if ($params[$name] === $meta['def'] || ($meta['def'] === NULL && is_scalar($params[$name]) && (string) $params[$name] === '')) {
				$params[$name] = NULL; // value transmit is unnecessary
			}
		}
	}

	/**
	 * Find entity by ID
	 *
	 * @param string $entityName
	 * @param int $id
	 *
	 * @return object|null
	 */
	protected function findStateEntityById($entityName, $id)
	{
		return $this->stateEntityManager->find($entityName, $id);
	}
}
