<?php
/**
 * Test: IPub\Application\TTranslator
 * @testCase
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec https://www.ipublikuj.eu
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

use IPubTests\Application\Libs;

require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'bootstrap.php';
require __DIR__ . DS . 'libs' . DS . 'Translator.php';
require __DIR__ . DS . 'libs' . DS . 'SecondTranslator.php';
require __DIR__ . DS . 'libs' . DS . 'RouterFactory.php';

/**
 * Translation trait tests
 *
 * @package        iPublikuj:Application!
 * @subpackage     Tests
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 */
class TranslatorTest extends Tester\TestCase
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

	/**
	 * @dataProvider dataUseTranslator
	 *
	 * @param string $toTranslate
	 * @param string $expected
	 */
	public function testPresenterUseTranslator(string $toTranslate, string $expected) : void
	{
		// Create test presenter
		$presenter = $this->createPresenter();

		// Create GET request
		$request = new Application\Request('Test', 'GET', ['action' => 'useTranslator', 'toTranslate' => $toTranslate]);
		// & fire presenter & catch response
		$response = $presenter->run($request);

		Assert::true($response instanceof Nette\Application\Responses\TextResponse);
		Assert::equal($expected, $response->getSource());
	}

	public function testPresenterChangeTranslator() : void
	{
		// Create test presenter
		$presenter = $this->createPresenter();

		// Create GET request
		$request = new Application\Request('Test', 'GET', ['action' => 'changeTranslator']);
		// & fire presenter & catch response
		$response = $presenter->run($request);

		Assert::true($response instanceof Nette\Application\Responses\TextResponse);
		Assert::equal('IPubTests\Application\Libs\SecondTranslator', $response->getSource());
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
		$config->addConfig(__DIR__ . DS . 'files' . DS . 'presenters.neon');

		return $config->createContainer();
	}
}

class TestPresenter extends UI\Presenter
{
	use IPub\Application\UI\TTranslator;

	public function renderUseTranslator(string $toTranslate)
	{
		$this->sendResponse(new Application\Responses\TextResponse($this->translator->translate($toTranslate)));
	}

	public function renderChangeTranslator()
	{
		$secondTranslator = new Libs\SecondTranslator();

		$this->setTranslator($secondTranslator);

		$this->sendResponse(new Application\Responses\TextResponse(get_class($this->getTranslator())));
	}
}

\run(new TranslatorTest());
