<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?float $balance = null;

    #[ORM\ManyToMany(targetEntity: Quest::class, inversedBy: 'users')]
    private Collection $quests;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: CompletedQuest::class, orphanRemoval: true)]
    private Collection $completedQuests;

    public function __construct()
    {
        $this->quests = new ArrayCollection();
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

    public function getBalance(): ?float
    {
        return $this->balance;
    }

    public function setBalance(float $balance): static
    {
        $this->balance = $balance;

        return $this;
    }

    /**
     * @return Collection<int, Quest>
     */
    public function getQuests(): Collection
    {
        return $this->quests;
    }

    public function addQuest(Quest $quest): static
    {
        if (!$this->quests->contains($quest)) {
            $this->quests->add($quest);
        }

        return $this;
    }

    public function removeQuest(Quest $quest): static
    {
        $this->quests->removeElement($quest);

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
            $completedQuest->setUser($this);
        }

        return $this;
    }

    public function removeCompletedQuest(CompletedQuest $completedQuest): static
    {
        if ($this->completedQuests->removeElement($completedQuest)) {
            // set the owning side to null (unless already changed)
            if ($completedQuest->getUser() === $this) {
                $completedQuest->setUser(null);
            }
        }

        return $this;
    }
}
