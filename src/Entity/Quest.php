<?php

namespace App\Entity;

use App\Repository\QuestRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuestRepository::class)]
class Quest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'quests')]
    private Collection $users;

    #[ORM\OneToMany(mappedBy: 'quest', targetEntity: CompletedQuest::class, orphanRemoval: true)]
    private Collection $completedQuests;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->completedQuests = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addQuest($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            $user->removeQuest($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, CompletedQuest>
     */
    public function getCompletedQuests(): Collection
    {
        return $this->completedQuests;
    }

    public function addCompletedQuest(CompletedQuest $completedQuest): static
    {
        if (!$this->completedQuests->contains($completedQuest)) {
            $this->completedQuests->add($completedQuest);
            $completedQuest->setQuest($this);
        }

        return $this;
    }

    public function removeCompletedQuest(CompletedQuest $completedQuest): static
    {
        if ($this->completedQuests->removeElement($completedQuest)) {
            // set the owning side to null (unless already changed)
            if ($completedQuest->getQuest() === $this) {
                $completedQuest->setQuest(null);
            }
        }

        return $this;
    }
}
