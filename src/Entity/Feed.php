<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\FeedRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\Common\Collections\ArrayCollection;
use Vich\UploaderBundle\Mapping\Annotation as Vich;


#[ORM\Entity(repositoryClass: FeedRepository::class)]
#[Vich\Uploadable]
class Feed
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable:true)]
    private ?string $title = null;


    #[ORM\Column(type:"datetime_immutable", nullable:true)]
    private $updatedAt;


    #[ORM\Column(length: 1600 , nullable:true)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?int $liked = null;

    #[ORM\Column(nullable: true)]
    private ?int $shared = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'feeds')]
    private ?User $author = null;

    #[Vich\UploadableField(mapping: 'feeds', fileNameProperty: 'imageName')]
    private ?File $imageFile = null;

    #[ORM\Column(nullable: true)]
    private ?string $imageName = null;

    #[ORM\OneToMany(mappedBy: 'feed', targetEntity: Like::class, orphanRemoval: true, cascade:["remove"])]
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


          /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
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
        $this->updatedAt = new DateTimeImmutable();
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
