<?php
/**
 * IObservable.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:Application!
 * @subpackage	Observing
 * @since		5.0
 *
 * @date		05.05.13
 */

namespace IPub\Application\Observing;

interface IObservable
{
	public function addObserver( IObserver $observer );

	public function clearChanged();

	public function setChanged();

	/** @return bool */
	public function hasChanged();

	/** @return int */
	public function countObservers();

	public function deleteObservers();

	public function notifyObservers();
}
