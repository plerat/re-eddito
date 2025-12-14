<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationControllerTest extends WebTestCase
{
    public function testRegistrationPage(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');
        $this->assertResponseIsSuccessful();

        $this->assertSelectorExists('form');
        $this->assertSelectorExists('input[name="register_user[email]"]');
        $this->assertSelectorExists('input[name="register_user[pseudo]"]');
        $this->assertSelectorExists('input[name="register_user[plainPassword][first]"]');
        $this->assertSelectorExists('input[name="register_user[plainPassword][second]"]');
    }

    public function testRegisterUser(): void {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get(EntityManagerInterface::class);

        $crawler = $client->request('GET', '/register');

        $form = $crawler->selectButton('Créer mon compte')->form();
        $form['register_user[email]'] = 'testRegister@test.xyz';
        $form['register_user[pseudo]'] = 'testRegister';
        $form['register_user[plainPassword][first]'] = '{7W5YE}?e7S#pq5c';
        $form['register_user[plainPassword][second]'] = '{7W5YE}?e7S#pq5c';

        $crawler = $client->submit($form);

        $userNotValidYet = $entityManager->getRepository(User::class)->findOneBy([
            'email' => 'testRegister@test.xyz',
        ]);

        $this->AssertInstanceOf(User::class, $userNotValidYet);
        $this->AssertEquals('testRegister@test.xyz', $userNotValidYet->getEmail());
        $this->AssertEquals('testRegister', $userNotValidYet->getPseudo());
        $this->AssertEquals(['ROLE_USER'], $userNotValidYet->getRoles());
        $this->AssertNotEquals('{7W5YE}?e7S#pq5c', $userNotValidYet->getPassword());
        $this->AssertFalse($userNotValidYet->getIsEnabled());

        $this->assertResponseRedirects("/register/send-verification-email/" . $userNotValidYet->getId());
    }
}
