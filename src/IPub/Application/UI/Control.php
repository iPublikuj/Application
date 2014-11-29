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
use Nette\Application;
use Nette\Localization;

abstract class Control extends Application\UI\Control
{
	/**
	 * @var Localization\ITranslator
	 */
	protected $translator;

	/**
	 * @param Localization\ITranslator $translator
	 */
	public function injectTranslator(Localization\ITranslator $translator)
	{
		$this->translator = $translator;
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

	/**
	 * Render component
	 */
	public function render()
	{
		$this->template->render();
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