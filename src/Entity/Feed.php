<?php

namespace App\Entity;

use App\Repository\FeedRepository;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: FeedRepository::class)]

class Feed
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 1600)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?int $liked = null;

    #[ORM\Column(nullable: true)]
    private ?int $shared = null;

    #[ORM\ManyToOne(inversedBy: 'feeds')]
    private ?User $author = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getLiked(): ?int
    {
        return $this->liked;
    }

    public function setLiked(?int $liked): static
    {
        $this->liked = $liked;

        return $this;
    }

    public function getShared(): ?int
    {
        return $this->shared;
    }

    public function setShared(?int $shared): static
    {
        $this->shared = $shared;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }
}
