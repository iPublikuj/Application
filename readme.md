# Application

[![Latest Stable Version](https://img.shields.io/packagist/v/ipub/application.svg?style=flat-square)](https://packagist.org/packages/ipub/application)
[![Composer Downloads](https://img.shields.io/packagist/dt/ipub/application.svg?style=flat-square)](https://packagist.org/packages/ipub/application)

Useful traits for presenters & components for [Nette Framework](http://nette.org/)

## Installation

The best way to install ipub/application is using  [Composer](http://getcomposer.org/):

```json
{
	"require": {
		"ipub/application": "dev-master"
	}
}
```

or

```sh
$ composer require ipub/application:@dev
```

## Usage

This package include a set of few useful traits. Traits are special classed which require PHP 5.4+

### Application\UI\TEntityCall

If you are using Doctrine in your app, you can easily convert variables which are used as parameters in handleX, actionX and renderX methods, so you don't need to call your model to load this entities.

```php
<?php

class SomePresenter extends Nette\Application\UI\Presenter
{
	use IPub\Application\UI\TEntityCall;

	/**
	 * @param Your\App\Namespace\Entities\Users\User $user
	 */
	public function actionEdit(Your\App\Namespace\Entities\Users\User $user)
	{
		$user->getName();
		....
	}
}
```

All what you have to do is include *trait* into your presenter or control and write proper data type. This trait try to find out your entity and load it from database.

### Application\UI\TRedirect

Are you using AJAX in your application? This trait will help you write less code. Now you don't have to create conditions for AJAX and nonAJAX requests.

```php
<?php

class SomePresenter extends Nette\Application\UI\Presenter
{
	use IPub\Application\UI\TRedirect;

	/**
	 * @param Your\App\Namespace\Entities\Users\User $user
	 */
	public function actionDefault()
	{
		$that = $this;

		....
		$form->getComponent('someForm');

		$form->onSuccess[] = function() use($that) {
			$that->go('your-path', ['arg1' => 'value1', 'arg2' => 'value2', ...], ['snippet-1', 'snippet-2', ...]);
		};
		....
	}

	public function handleDoSomethig()
	{
		...

		$that->go('other-path', NULL, ['snippet-1', 'snippet-2', ...]);
	}
}
```

Methods in this *trait* will process your request, check if request was done by AJAX and check the current page and page you requested for redirecting. In case this pages are same, trait will do usual snippets redrawing and it is all. In case pages are different, trait will do forwarding. For nonAJAX request is classic redirect processed.

### Application\UI\TTranslator

If you are bored of writing include methods or setters for translators, just use this simple trait. This piece of code will insert inject method and setter and getter for your localization translator.

```php
<?php

class SomePresenter extends Nette\Application\UI\Presenter
{
	use IPub\Application\UI\TTranslator;

	/**
	 * @param Your\App\Namespace\Entities\Users\User $user
	 */
	public function actionEdit(Your\App\Namespace\Entities\Users\User $user)
	{
		...

		$this->translator->translate('string-to-translate');
		// or
		$this->getTranslator()->translate('string-to-translate');

		....
	}
}
```
