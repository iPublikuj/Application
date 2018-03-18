<?php
/**
 * Test: IPub\Application\TRedirect
 * @testCase
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:Application!
 * @subpackage     Tests
 * @since          1.1.0
 *
 * @date           10.12.16
 */

declare(strict_types = 1);

namespace IPubTests\Application;

use Nette;
use Nette\Application;
use Nette\Application\UI;

use Tester;
use Tester\Assert;

use IPub;

require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'bootstrap.php';
require __DIR__ . DS . 'libs' . DS . 'Translator.php';
require __DIR__ . DS . 'libs' . DS . 'SecondTranslator.php';
require __DIR__ . DS . 'libs' . DS . 'RouterFactory.php';

/**
 * Redirect trait tests
 *
 * @package        iPublikuj:Application!
 * @subpackage     Tests
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
class RedirectTest extends Tester\TestCase
{
	/**
	 * @var Application\IPresenterFactory
	 */
	private $presenterFactory;

	/**
	 * @var Nette\DI\Container
	 */
	private $container;

	/**
	 * @return array[]|array
	 */
	public function dataUseTranslator() : array
	{
		return [
			['text-to-translate', 'text-to-translate'],
			['Yes, that will be translated', 'Yes, that will be translated'],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function setUp() : void
	{
		parent::setUp();

		$this->container = $this->createContainer();

		// Get presenter factory from container
		$this->presenterFactory = $this->container->getByType(Nette\Application\IPresenterFactory::class);
	}

	public function testPresenterRedirect() : void
	{
		// Create test presenter
		$presenter = $this->createPresenter();

		// Create GET request
		$request = new Application\Request('Test', 'GET', ['action' => 'redirect', 'destination' => 'End:show']);
		// & fire presenter & catch response
		$response = $presenter->run($request);

		Assert::true($response instanceof Nette\Application\Responses\RedirectResponse);
		Assert::equal('http:///end/show', $response->getUrl());
	}

	public function testPresenterForward() : void
	{
		// Create test presenter
		$presenter = $this->createPresenter();

		// Create GET request
		$request = new Application\Request('Test', 'GET', ['action' => 'forward', 'destination' => 'End:show']);
		// & fire presenter & catch response
		$response = $presenter->run($request);

		Assert::true($response instanceof Nette\Application\Responses\ForwardResponse);
		Assert::equal('FORWARD', $response->getRequest()->getMethod());
		Assert::equal('End', $response->getRequest()->getPresenterName());
		Assert::equal(['action' => 'show'], $response->getRequest()->getParameters());
	}

	public function testPresenterAjaxRedirect() : void
	{
		// Create test presenter
		$presenter = $this->createPresenter();

		// Create GET request
		$request = new Application\Request('Test', 'POST', ['action' => 'ajaxRedirect']);
		// & fire presenter & catch response
		$response = $presenter->run($request);

		Assert::true($response instanceof Nette\Application\Responses\TextResponse);
		Assert::equal('GET request finished', $response->getSource());
	}

	/**
	 * @return Application\IPresenter
	 */
	protected function createPresenter() : Application\IPresenter
	{
		// Create test presenter
		$presenter = $this->presenterFactory->createPresenter('Test');
		// Disable auto canonicalize to prevent redirection
		$presenter->autoCanonicalize = FALSE;

		return $presenter;
	}

	/**
	 * @return Nette\DI\Container
	 */
	private function createContainer() : Nette\DI\Container
	{
		$config = new Nette\Configurator();
		$config->setTempDirectory(TEMP_DIR);

		$config->addConfig(__DIR__ . DS . 'files' . DS . 'config.neon');

		$version = getenv('NETTE');

		$config->addConfig(__DIR__ . DS . 'files' . DS . 'presenters.neon');

		return $config->createContainer();
	}
}



class TestPresenter extends UI\Presenter
{
	use IPub\Application\UI\TRedirect;

	public function actionRedirect(string $destination)
	{
		$this->go($destination);
	}

	public function actionForward(string $destination)
	{
		$this->go($destination);
	}

	public function actionAjaxRedirect()
	{
		if ($this->getRequest()->isMethod('post')) {
			$this->go('this');
		}

		$this->sendResponse(new Application\Responses\TextResponse('GET request finished'));
	}

	/**
	 * {@inheritdoc}
	 */
	public function isAjax() : bool
	{
		if (in_array($this->action, ['ajaxRedirect', 'forward'], TRUE)) {
			return TRUE;
		}

		return parent::isAjax();
	}
}

class EndPresenter extends UI\Presenter
{
	public function renderDefault()
	{
		$this->sendResponse(new Application\Responses\TextResponse('Redirect complete'));
	}
}

\run(new RedirectTest());
