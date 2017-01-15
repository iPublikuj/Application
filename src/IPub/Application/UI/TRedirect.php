<?php
/**
 * TRedirect.php
 *
 * @copyright      More in license.md
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:Application!
 * @subpackage     UI
 * @since          1.0.0
 *
 * @date           05.02.15
 */

declare(strict_types = 1);

namespace IPub\Application\UI;

use Nette;
use Nette\Application;
use Nette\Application\Responses;

/**
 * Add improved redirects & forwarding into presenters & components
 *
 * @package        iPublikuj:Application!
 * @subpackage     UI
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 *
 * @method Application\UI\Presenter getPresenter()
 * @method string getUniqueId()
 * @method redrawControl($snippet = NULL, $redraw = TRUE)
 * @method redirect($code, $destination = NULL, $args = [])
 */
trait TRedirect
{
	/**
	 * Redirect only if not ajax
	 *
	 * @param string $destination
	 * @param array $args
	 * @param array $snippets
	 *
	 * @return void
	 *
	 * @throws Application\AbortException
	 */
	final public function go($destination, $args = [], $snippets = [])
	{
		// Get presenter object
		$presenter = ($this instanceof Application\UI\Presenter) ? $this : $this->getPresenter();

		if ($presenter->isAjax()) {
			foreach ($snippets as $snippet) {
				$this->redrawControl($snippet);
			}

			if ($destination !== 'this') {
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
	 *
	 * @return void
	 *
	 * @throws Application\AbortException
	 */
	public function forward($destination, $args = [])
	{
		if (!$this->isPresenter()) {
			$name = $this->getUniqueId();

			if ($destination != 'this') {
				$destination = "$name-$destination";
			}

			$params = [];

			foreach ($args as $key => $val) {
				$params["$name-$key"] = $val;
			}

			// Process forwarding in control
			$this->getPresenter()->forward($destination, $params);

		} else {
			// Process forwarding in presenter
			parent::forward($destination, $args);
		}
	}

	/**
	 * @return bool
	 */
	private function isPresenter() : bool
	{
		if ($this instanceof Application\UI\Presenter) {
			return TRUE;
		}

		return FALSE;
	}
}
