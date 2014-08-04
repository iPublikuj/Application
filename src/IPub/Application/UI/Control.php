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
use Nette\Http;
use Nette\Localization;

use IPub\Application\Observing\IObservable,
	IPub\Application\Observing\IObserver;

abstract class Control extends Nette\Application\UI\Control implements IObservable
{
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
	 * @var Http\IRequest
	 */
	protected $httpRequest;

	/**
	 * @var Http\Session
	 */
	protected $session;

	/**
	 * @param Localization\ITranslator $translator
	 */
	public function injectTranslator(Localization\ITranslator $translator)
	{
		$this->translator = $translator;
	}

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
}