<?php

namespace App\Tests\Functional\Controller;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PostControllerTest extends WebTestCase
{
    public function testCreatePostWithoutFile(): void
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get(EntityManagerInterface::class);

        $user = $entityManager->getRepository(User::class)->findOneBy([
            'email' => 'user@user.xyz',
        ]);
        $client->loginUser($user);

        $crawler = $client->request('GET', '/post/new');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');

        $form = $crawler->selectButton('Save')->form([
            'post_new[title]' => 'test post title',
            'post_new[content]' => 'test post content',
        ]);
        $client->submit($form);

        $post = $entityManager->getRepository(Post::class)->findOneBy([
            'title' => 'test post title',
        ]);

        $this->assertInstanceOf(Post::class, $post);
        $this->assertEquals('test post content', $post->getContent());

        $this->assertResponseRedirects('/', 303);
    }
}
