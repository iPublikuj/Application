<?php
/**
 * TTranslator.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec https://www.ipublikuj.eu
 * @package        iPublikuj:Application!
 * @subpackage     UI
 * @since          1.0.0
 *
 * @date           05.02.15
 */

declare(strict_types = 1);

namespace IPub\Application\UI;

use Nette\Localization;

/**
 * Inject translator interface into presenters and components
 *
 * @package        iPublikuj:Application!
 * @subpackage     UI
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
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
	public function injectTranslator(Localization\ITranslator $translator) : void
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
	public function setTranslator(Localization\ITranslator $translator) : void
	{
		$this->translator = $translator;
	}

	/**
	 * Get app translator service
	 *
	 * @return Localization\ITranslator|NULL
	 */
	public function getTranslator() : ?Localization\ITranslator
	{
		if ($this->translator instanceof Localization\ITranslator) {
			return $this->translator;
		}

		return NULL;
	}
}
