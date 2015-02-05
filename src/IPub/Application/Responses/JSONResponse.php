<?php
/**
 * JsonResponse.php
 *
 * @copyright	Vice v copyright.php
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:Application!
 * @subpackage	Responses
 * @since		5.0
 *
 * @date		10.02.13
 */

namespace IPub\Application\Responses;

use Nette;
use Nette\Application;
use Nette\Http;
use Nette\Utils;

class JsonResponse extends Nette\Object implements Application\IResponse
{
	/**
	 * @var string
	 */
	protected $contentType;

	/**
	 * @var array
	 */
	protected $payload = [];

	/**
	 * @var bool
	 */
	protected $compileVariables;

	/**
	 * @param $compileVariables
	 * @param string $contentType
	 */
	public function __construct($compileVariables, $contentType = 'application/json')
	{
		$this->compileVariables = (bool) $compileVariables;
		$this->contentType = $contentType;
	}

	/**
	 * @return array
	 */
	public function getPayload()
	{
		return $this->payload;
	}

	/**
	 * @param array $payload
	 */
	public function setPayload(array $payload)
	{
		$this->payload = $payload;
	}

	/**
	 * @param string
	 * @param mixed
	 *
	 * @return $this
	 *
	 * @throws Nette\InvalidStateException
	 */
	public function add($name, $value)
	{
		if (array_key_exists($name, $this->payload)) {
			throw new Nette\InvalidStateException("A variable '$name' already exists.'");
		}

		return $this->set($name, $value);
	}

	/**
	 * @param $name
	 * @param $value
	 *
	 * @return $this
	 */
	public function set($name, $value)
	{
		if ($value instanceof \DateTime || $value instanceof Utils\DateTime) {
			$value = $value->getTimestamp();
		}

		$this->payload[$name] = $value;

		return $this;
	}

	/**
	 * @param string
	 *
	 * @return bool
	 */
	public function __isset($name)
	{
		return isset($this->payload[$name]);
	}

	/**
	 * @param $name
	 *
	 * @return mixed
	 *
	 * @throws Nette\InvalidStateException
	 */
	public function &__get($name)
	{
		if (!array_key_exists($name, $this->payload)) {
			throw new Nette\InvalidStateException("The variable '$name' does not exist.'");
		}

		return $this->payload[$name];
	}

	/**
	 * @param string
	 * @param mixed
	 * @return void
	 */
	public function __set($name, $value)
	{
		$this->set($name, $value);
	}

	/**
	 * @param string
	 * @return void
	 */
	public function __unset($name)
	{
		unset($this->payload[$name]);
	}

	/**
	 * @param Http\IRequest $httpRequest
	 * @param Http\IResponse $httpResponse
	 */
	public function send(Http\IRequest $httpRequest, Http\IResponse $httpResponse)
	{
		$httpResponse->setContentType($this->contentType);
		$httpResponse->setExpiration(FALSE);

		if ($this->compileVariables and count($this->payload) === 1) {
			$keys = array_keys($this->payload);
			$this->payload = $this->payload[$keys[0]];
		}

		echo Utils\Json::encode($this->payload);
	}
}