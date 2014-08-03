<?php

namespace Yoda\UserBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\StringInput;

class RegisterControllerTest extends WebTestCase
{

	protected static $application;

	protected function setUp()
	{
		self::runCommand('doctrine:fixtures:load --env=test --purge-with-truncate --no-interaction');
	}


	protected static function runCommand($command)
	{
		return self::getApplication()->run(new StringInput($command));
	}

	protected static function getApplication()
	{
		if (null === self::$application) {
			$client = static::createClient();

			self::$application = new Application($client->getKernel());
			self::$application->setAutoExit(false);
		}

		return self::$application;
	}


	public function testRegister()
	{
		$client = static::createClient();

		$crawler = $client->request('GET', '/register');
		$response = $client->getResponse();

		$this->assertEquals(200, $response->getStatusCode());
		$this->assertContains('Registration', $response->getContent());

		$usernameValue = $crawler
			->filter('#user_register_username')
			->attr('value');

		$this->assertEquals('Leia', $usernameValue);

		$form = $crawler->selectButton('Register!')->form();
		$client->submit($form);


		$this->assertEquals(200, $client->getResponse()->getStatusCode());
		$this->assertRegexp(
			'/This value should not be blank/',
			$client->getResponse()->getContent()
		);

		$form = $crawler->selectButton('Register!')->form();
		$form['user_register[username]'] 				= 'user4';
		$form['user_register[email]'] 					= 'user4@user.com';
		$form['user_register[plainPassword][first]'] 	= 'P3ssword';
		$form['user_register[plainPassword][second]'] 	= 'P3ssword';

		$client->submit($form);

		$this->assertTrue($client->getResponse()->isRedirect());
		$client->followRedirect();
		$this->assertContains(
			'Welcome to the Death Star! Have a magical day!',
			$client->getResponse()->getContent()
		);
	}
}
