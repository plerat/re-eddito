<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255,type: Types::STRING, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255,type: Types::STRING)]
    private ?string $password = null;

    private ?string $plainPassword = null;

    #[ORM\Column(length: 255, type: Types::STRING, unique: true)]
    private ?string $pseudo = null;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column(type: types::BOOLEAN)]
    private ?bool $isDeleted = False;

    #[ORM\Column(type: types::BOOLEAN)]
    private ?bool $isEnabled = False;

    #[ORM\OneToMany(targetEntity: Token::class, mappedBy: 'user', cascade: ['persist'])]
    private ?Collection $tokens;

    #[ORM\OneToMany(targetEntity: Post::class, mappedBy: 'user', cascade: ['persist'])]
    private ?Collection $posts;


    public function __construct()
    {
        $this->tokens = new ArrayCollection();
        $this->posts = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getUserIdentifier(): string
    {
       return $this->email;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $password): self
    {
        $this->plainPassword = $password;

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(?string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(?array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function GetIsDeleted(): ?bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(?bool $isDeleted): self
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    public function GetIsEnabled(): ?bool
    {
        return $this->isEnabled;
    }

    public function setIsEnabled(?bool $isEnabled): self
    {
        $this->isEnabled = $isEnabled;

        return $this;
    }

    public function getTokens(): Collection
    {
        return $this->tokens;
    }

    public function setToken(Collection $tokens): self
    {
        $this->tokens = $tokens;
        return $this;
    }

    public function addToken(Token $token): self
    {
        $token->setUser($this);
        $this->tokens->add($token);
        return $this;
    }

    public function getPost(): Collection
    {
        return $this->posts;
    }

    public function setPost(Collection $posts): self
    {
        $this->posts = $posts;
        return $this;
    }

    public function addPost(Post $post): self
    {
        $post->setCreatedBy($this);
        $this->posts->add($post);
        return $this;
    }
}
