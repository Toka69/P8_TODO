<?php

namespace App\Tests\Controller;


use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Exception\TimeoutException;
use Symfony\Component\Panther\PantherTestCase;

class UserControllerTest extends PantherTestCase
{
    /**
     * @throws NoSuchElementException
     * @throws TimeoutException
     */
    public function testTasksList(): void
    {
        $client = static::createPantherClient();
        $crawler = $client->request('GET', '/login');

        $client->waitForVisibility('#login-form');
        $form = $crawler->filter('#login-form')->form(
            [
                '_username' => 'user',
                '_password' => 'test'
            ]
        );

        $client->submit($form);

    }
}
