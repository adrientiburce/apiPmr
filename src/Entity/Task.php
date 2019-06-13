<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TaskRepository")
 */
class Task implements JsonSerializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $checked;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Todos", inversedBy="tasks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $todos;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $url;

    
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

    public function jsonSerialize()
    {
        return array(
            'id' => $this->id,
            'label' => $this->name,
            'checked' => $this->checked,
            'url' => $this->url,
        );
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
}
