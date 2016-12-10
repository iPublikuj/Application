<?php
/**
 * TTranslator.php
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
use Nette\Localization;

/**
 * Inject translator interface into presenters and components
 *
 * @package        iPublikuj:Application!
 * @subpackage     UI
 */
trait TTranslator
{
	/**
	 * @var Localization\ITranslator
	 */
	protected $translator;

	/**
	 * @param Localization\ITranslator $translator
	 *
	 * @return void
	 */
	public function injectTranslator(Localization\ITranslator $translator)
	{
		$this->translator = $translator;
	}

	/**
	 * Set translator service
	 *
	 * @param Localization\ITranslator $translator
	 * 
	 * @return void
	 */
	public function setTranslator(Localization\ITranslator $translator)
	{
		$this->translator = $translator;
	}

	/**
	 * Get app translator service
	 *
	 * @return Localization\ITranslator|NULL
	 *
	 * @return void
	 */
	public function getTranslator()
	{
		if ($this->translator instanceof Localization\ITranslator) {
			return $this->translator;
		}

		return NULL;
	}
}
