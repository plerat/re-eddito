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
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
#[UniqueEntity(fields: ['pseudo'], message: 'There is already an account with this nickname')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255,type: Types::STRING, unique: true)]
    #[Assert\NotBlank(message: 'A valid email is required')]
    #[Assert\Email(message: 'This email is not valid')]
    private ?string $email = null;

    #[ORM\Column(length: 255,type: Types::STRING)]
    private ?string $password = null;

    #[Assert\NotBlank(message: 'A valid password is required')]
    #[Assert\PasswordStrength(
        minScore: Assert\PasswordStrength::STRENGTH_MEDIUM,
        message: "Your password is too weak : make sure it's at least 16 characters with capital letters, numbers and symbols"
    )]
    private ?string $plainPassword = null;

    #[ORM\Column(length: 255, type: Types::STRING, unique: true)]
    #[Assert\NotBlank(message: 'A nickname is required')]
    private ?string $pseudo = null;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column(type: types::BOOLEAN)]
    private ?bool $isDeleted = False;

    #[ORM\Column(type: types::BOOLEAN)]
    private ?bool $isEnabled = False;

    #[ORM\OneToMany(targetEntity: Token::class, mappedBy: 'user', cascade: ['persist'])]
    private ?Collection $tokens;

    #[ORM\OneToMany(targetEntity: Post::class, mappedBy: 'createdBy', cascade: ['persist'])]
    private ?Collection $posts;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'createdBy')]
    private Collection $comments;


    public function __construct()
    {
        $this->tokens = new ArrayCollection();
        $this->posts = new ArrayCollection();
        $this->comments = new ArrayCollection();
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

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setCreatedBy($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getCreatedBy() === $this) {
                $comment->setCreatedBy(null);
            }
        }

        return $this;
    }
}
