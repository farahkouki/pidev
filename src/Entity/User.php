<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(message: 'Veuillez saisir une adresse e-mail')]
    #[Assert\Email(message: 'Veuillez saisir une adresse e-mail valide')]
    #[Assert\Regex(pattern: '/@gmail\.com$/', message: 'L\'adresse e-mail doit être de domaine @gmail.com')]
    private ?string $email = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Veuillez saisir un nom')]
    #[Assert\Type(type: 'string', message: 'Le nom doit être une chaîne de caractères')]
    #[Assert\Regex(pattern: '/^[a-zA-Z]+$/', message: 'Le nom doit contenir uniquement des lettres')]
    private ?string $nom = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Veuillez saisir un prénom')]
    #[Assert\Type(type: 'string', message: 'Le prénom doit être une chaîne de caractères')]
    #[Assert\Regex(pattern: '/^[a-zA-Z]+$/', message: 'Le prénom doit contenir uniquement des lettres')]
    private ?string $prenom = null;

    #[ORM\Column(type: 'date')]
    #[Assert\NotBlank(message: 'Veuillez saisir une date de naissance')]
    #[Assert\Range(
        min: '1975-01-01',
        max: '2012-12-31',
        notInRangeMessage: 'La date de naissance doit être entre {{ min }} et {{ max }}'
    )]
    private ?\DateTimeInterface $dateNaissance = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Veuillez saisir un numéro de téléphone')]
    #[Assert\Type(type: 'string', message: 'Le numéro de téléphone doit être une chaîne de caractères')]
    #[Assert\Length(
        max: 8,
        maxMessage: 'Le numéro de téléphone ne doit pas dépasser {{ limit }} chiffres'
    )]
    private ?string $numeroTelephone = null;

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    #[ORM\Column]
    private array $roles = [];

    // ... autres méthodes et propriétés

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    #[Assert\NotBlank(message: 'Veuillez saisir un nom')]
    #[Assert\Type(type: 'string', message: 'Le nom doit être une chaîne de caractères')]
    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    #[Assert\NotBlank(message: 'Veuillez saisir un prénom')]
    #[Assert\Type(type: 'string', message: 'Le prénom doit être une chaîne de caractères')]
    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(?\DateTimeInterface $dateNaissance): self
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    public function getNumeroTelephone(): ?string
    {
        return $this->numeroTelephone;
    }

    public function setNumeroTelephone(?string $numeroTelephone): self
    {
        $this->numeroTelephone = $numeroTelephone;

        return $this;
    }

    public function __toString()
    {
        return $this->roles[0] ?? 'ROLE_USER';
    }
}