<?php

namespace App\Entity;

use App\Repository\FeedRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'feeds')]
    private ?User $author = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\OneToMany(mappedBy: 'feed', targetEntity: Like::class, orphanRemoval: true)]
    private Collection $likes;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'feed')]
    private Collection $commentss;

    public function __construct()
    {
        $this->likes = new ArrayCollection();
        $this->commentss = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Like>
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Like $like): static
    {
        if (!$this->likes->contains($like)) {
            $this->likes->add($like);
            $like->setFeed($this);
        }

        return $this;
    }

    public function removeLike(Like $like): static
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getFeed() === $this) {
                $like->setFeed(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getCommentss(): Collection
    {
        return $this->commentss;
    }

    public function addCommentss(Comment $commentss): static
    {
        if (!$this->commentss->contains($commentss)) {
            $this->commentss->add($commentss);
            $commentss->setComments($this);
        }

        return $this;
    }

    public function removeCommentss(Comment $commentss): static
    {
        if ($this->commentss->removeElement($commentss)) {
            // set the owning side to null (unless already changed)
            if ($commentss->getComments() === $this) {
                $commentss->setComments(null);
            }
        }

        return $this;
    }
}
