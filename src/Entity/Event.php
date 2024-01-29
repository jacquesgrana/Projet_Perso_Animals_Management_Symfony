<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Library\WeekPatternLibrary;
use App\Library\CustomLibrary;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 128)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $comment = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $start = null;

    #[ORM\Column]
    private ?int $duration = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    #[ORM\JoinColumn(nullable: false)]
    private ?EventCategory $category = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    #[ORM\JoinColumn(nullable: false)]
    private ?EventStatus $status = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    #[ORM\JoinColumn(nullable: false)]
    private ?EventPriority $priority = null;

    #[ORM\ManyToMany(targetEntity: Animal::class, inversedBy: 'events')]
    private Collection $animals;

    #[ORM\ManyToOne(inversedBy: 'events')]
    private ?User $user = null;

    #[ORM\Column(nullable: true)]
    private ?int $patternsNumber = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $weekPattern = null;

    public function __construct()
    {
        $this->animals = new ArrayCollection();
        $this->patternsNumber = 1;
    }

    public function getDaysNamesFromPattern(): array
    {
        return WeekPatternLibrary::getDayNames($this->getWeekPattern());
    }

    // TODO mettre dans CustomLibrary
    public function getColorFromPriority(): string {
        return CustomLibrary::getColorFromPriority($this->getPriority()->getName());
        /*
        //dd($this->getPriority()->getName());
        switch ($this->getPriority()->getName()) {
            case 'Non Urgente' :
                return 'green';
                break;
            case 'Normale' :
                return 'blue';
                break;
            case 'Urgente' :
                return 'orange';
                break;
            case 'Très Urgente' :
                return 'red';
                break;
            default :
                return 'white';
                break;
        }*/
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(\DateTimeInterface $start): static
    {
        $this->start = $start;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getCategory(): ?EventCategory
    {
        return $this->category;
    }

    public function setCategory(?EventCategory $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getStatus(): ?EventStatus
    {
        return $this->status;
    }

    public function setStatus(?EventStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getPriority(): ?EventPriority
    {
        return $this->priority;
    }

    public function setPriority(?EventPriority $priority): static
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * @return Collection<int, Animal>
     */
    public function getAnimals(): Collection
    {
        return $this->animals;
    }

    public function addAnimal(Animal $animal): static
    {
        if (!$this->animals->contains($animal)) {
            $this->animals->add($animal);
        }

        return $this;
    }

    public function removeAnimal(Animal $animal): static
    {
        $this->animals->removeElement($animal);

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getPatternsNumber(): ?int
    {
        return $this->patternsNumber;
    }

    public function setPatternsNumber(?int $patternsNumber): static
    {
        $this->patternsNumber = $patternsNumber;

        return $this;
    }

    public function getWeekPattern(): ?string
    {
        return $this->weekPattern;
    }

    public function setWeekPattern(?string $weekPattern): static
    {
        $this->weekPattern = $weekPattern;

        return $this;
    }
}
