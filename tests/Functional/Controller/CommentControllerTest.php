<?php

namespace App\Tests\Functional\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CommentControllerTest extends WebTestCase
{
    public function testCreateComment(): void
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get(EntityManagerInterface::class);

        $user = $entityManager->getRepository(User::class)->findOneBy([
            'email' => 'user@user.xyz']);

        $client->loginUser($user);
        $postId = $entityManager->getRepository(Post::class)->findOneBy([
            'title' => 'les chaises de bar'])->getId();

        $crawler = $client->request('GET', 'post/'. $postId . '/comment/new');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');

        $form = $crawler->selectButton('Valider')->form([
            'comment[content]' => 'test comment content']);
        $client->submit($form);

        $comment = $entityManager->getRepository(Comment::class)->findOneBy([
            'content' => 'test comment content']);

        $this->assertInstanceOf(Comment::class, $comment);

        $this->assertResponseRedirects('/post/' . $postId, 303);
    }
}
