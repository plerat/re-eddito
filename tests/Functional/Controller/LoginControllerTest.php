<?php

namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginControllerTest extends WebTestCase
{
    public function testLoginPage(): void {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $this->assertResponseIsSuccessful();
    }

    public function testLoginWithUser(): void {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Login')->form();
        $form['_username'] = 'user@user.xyz';
        $form['_password'] = 'user';

        $crawler = $client->submit($form);
        $this->assertResponseRedirects('/');
    }
}
