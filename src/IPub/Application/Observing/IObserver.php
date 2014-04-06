<?php
/**
 * IObserver.php
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

interface IObserver
{
	public function update(IObservable $eventSource, $args);
}