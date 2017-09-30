<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var Task[]
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Task", mappedBy="creator")
     */
    private $tasks;

    /**
     * @var Category[]
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Category", mappedBy="creator")
     */
    private $categories;

    public function __construct()
    {
        parent::__construct();
        $this->tasks = new ArrayCollection();
        $this->categories = new ArrayCollection();
    }

    public function getTasks()
    {
        return $this->tasks;
    }

    public function addTask(Task $task)
    {
        $this->tasks[] = $task;

        return $this;
    }

    public function getCategories()
    {
        return $this->categories;
    }

    public function addCategories(Category $category)
    {
        $this->categories[] = $category;

        return $this;
    }
}

