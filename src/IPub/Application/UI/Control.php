<?php
/**
 * Control.php
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
use Nette\ComponentModel;
use Nette\Localization;

use IPub\Application\Observing\IObservable,
	IPub\Application\Observing\IObserver;

abstract class Control extends Nette\Application\UI\Control implements IObservable
{
	/**
	 * Define available control types
	 */
	const CONTROL_TYPE_COMPONENT	= 'component';
	const CONTROL_TYPE_FORM			= 'form';

	/**
	 * @var array of registered observers
	 */
	protected $observers = array();

	/**
	 * @var bool if is set TRUE, than object is changed
	 */
	protected $changed = FALSE;

	/**
	 * @var Localization\ITranslator
	 */
	protected $translator;

	/**
	 * @var string
	 */
	protected $controlType = self::CONTROL_TYPE_COMPONENT;

	/**
	 * @var string
	 */
	protected $controlName;

	/**
	 * @param Localization\ITranslator $translator
	 */
	public function injectTranslator(Localization\ITranslator $translator)
	{
		$this->translator = $translator;
	}

	/**
	 * @param ComponentModel\IContainer $parent
	 * @param string $name
	 */
	public function __construct(
		ComponentModel\IContainer $parent = NULL, $name = NULL
	) {
		parent::__construct($parent, $name);

		// Get component namespace
		$namespace = $this->getReflection()->getNamespaceName();
		// Explode it to all parts
		$parts = explode('\\', $namespace);

		// Get control name and group (components or forms)
		$this->controlName = array_pop($parts);
		$this->controlType = strtolower(array_pop($parts)) == 'forms' ? self::CONTROL_TYPE_FORM : self::CONTROL_TYPE_COMPONENT;
	}

	/**
	 * Set control translator service
	 *
	 * @param Localization\ITranslator $translator
	 *
	 * @return $this
	 */
	public function setTranslator(Localization\ITranslator $translator)
	{
		$this->translator = $translator;

		return $this;
	}

	/**
	 * Get control translator service
	 *
	 * @return Localization\ITranslator
	 */
	public function getTranslator()
	{
		return $this->translator;
	}

	/**
	 * Set control type flag
	 *
	 * @param string $type
	 *
	 * @return $this
	 *
	 * @throws \Nette\InvalidArgumentException
	 */
	public function setControlType($type)
	{
		if (!in_array($type, array(self::CONTROL_TYPE_FORM, self::CONTROL_TYPE_COMPONENT))) {
			throw new Nette\InvalidArgumentException('Invalid control type given');
		}

		$this->controlType = $type;

		return $this;
	}

	/**
	 * Get control type
	 *
	 * @return string
	 */
	public function getControlType()
	{
		return (string) $this->controlType;
	}

	/**
	 * Get info if control is form type
	 *
	 * @return bool
	 */
	public function isControlForm()
	{
		return $this->controlType === self::CONTROL_TYPE_FORM;
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
		if ($this->getPresenter()->isAjax()) {
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
	 * Forward request to another
	 *
	 * @param string $destination
	 * @param array $args
	 */
	public function forward($destination, $args = [])
	{
		$name = $this->getUniqueId();

		if ($destination != 'this') {
			$destination = "$name-$destination";
		}

		$params = array();

		foreach($args as $key => $val) {
			$params["$name-$key"] = $val;
		}

		$this->getPresenter()->forward($destination, $params);
	}

	/**
	 * Check if system is on production or development mode
	 *
	 * @return bool
	 */
	protected function isProductionMode()
	{
		return (bool) $this->getPresenter()->getParameter('productionMode', FALSE);
	}

	public function addObserver(IObserver $observer)
	{
		if ($this->hasObserver($observer)) {
			return;
		}

		$this->observers[] = $observer;
	}

	protected function hasObserver(IObserver $observer)
	{
		foreach ($this->observers as $o) {
			if ($o === $observer) {
				return TRUE;
			}
		}

		return FALSE;
	}

	public function countObservers()
	{
		return count($this->observers);
	}

	public function deleteObservers()
	{
		$this->observers = array();
	}

	public function hasChanged()
	{
		return $this->changed;
	}

	public function setChanged()
	{
		$this->changed = TRUE;
	}

	public function clearChanged()
	{
		$this->changed = FALSE;
	}

	public function notifyObservers($args = NULL)
	{
		if (!$this->hasChanged()) {
			return;
		}

		foreach ($this->observers as $observer) {
			/* @var $observer IObserver */
			$observer->update($this, $args);
		}
	}

	/**
	 * Convert component name to string representation
	 *
	 * @return string
	 */
	public function __toString()
	{
		$class = explode('\\', get_class($this));

		return end($class);
	}
}