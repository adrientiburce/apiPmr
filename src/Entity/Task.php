<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TaskRepository")
 * @ApiResource(
 *     denormalizationContext={"groups"={"write"}}
 * )
 */
class Task
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups("write")
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $checked;

    /**
     * @Groups("write")
     * @ORM\ManyToOne(targetEntity="App\Entity\Todos", inversedBy="tasks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $todos;

    /**
     * @Groups("write")
     * @Assert\Regex("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i")
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $url;

    public function __construct()
    {
        $this->checked = 0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getChecked()
    {
        return $this->checked;
    }

    public function setChecked($checked): self
    {
        $this->checked = $checked;
        return $this;
    }


    public function getTodos(): ?Todos
    {
        return $this->todos;
    }

    public function setTodos(?Todos $todos): self
    {
        $this->todos = $todos;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

/*    public function jsonSerialize()
    {
        return array(
            'id' => $this->id,
            'label' => $this->name,
            'checked' => $this->checked,
            'url' => $this->url,
        );
    }*/
}
