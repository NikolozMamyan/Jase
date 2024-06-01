<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    private ?Feed $feed = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    private ?User $userCommented = null;

    #[ORM\Column(type:"string")]
    private string $content;

    #[ORM\ManyToOne(inversedBy: 'commentss')]
    private ?Feed $comments = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFeed(): ?Feed
    {
        return $this->feed;
    }

    public function setFeed(?Feed $feed): self
    {
        $this->feed = $feed;
        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getUserCommented(): ?User
    {
        return $this->userCommented;
    }

    public function setUserCommented(?User $userCommented): self
    {
        $this->userCommented = $userCommented;
        return $this;
    }

    public function getComments(): ?Feed
    {
        return $this->comments;
    }

    public function setComments(?Feed $comments): static
    {
        $this->comments = $comments;

        return $this;
    }
}
