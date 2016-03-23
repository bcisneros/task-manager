<?php

namespace TaskManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Comment
 *
 * @ORM\Table(name="comments")
 * @ORM\Entity(repositoryClass="TaskManagerBundle\Repository\CommentRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Comment
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string", length=2000)
     * @Assert\Length(
     *      max = 2000,
     *      maxMessage = "Comment cannot be longer than {{ limit }} characters")
     */
    private $comment;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creation_date", type="datetime")
     */
    private $creationDate;

    /**
     * @var int
     *
     * @ORM\Column(name="task_id", type="integer")
     */
    private $taskId;

    /**
     * @ORM\ManyToOne(targetEntity="Task", inversedBy="comments")
     * @ORM\JoinColumn(name="task_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $task;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return Comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set creationDate
     *
     * @param \DateTime $creationDate
     *
     * @return Comment
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreationDateValue()
    {
        if ($this->creationDate == null) {
            $this->creationDate = new \DateTime();
        }
    }

    /**
     * Get creationDate
     *
     * @return \DateTime
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * Set taskId
     *
     * @param integer $taskId
     *
     * @return Comment
     */
    public function setTaskId($taskId)
    {
        $this->taskId = $taskId;

        return $this;
    }

    /**
     * Get taskId
     *
     * @return int
     */
    public function getTaskId()
    {
        return $this->taskId;
    }

    /**
     * Set task
     *
     * @param \TaskManagerBundle\Entity\Task $task
     *
     * @return Comment
     */
    public function setTask(\TaskManagerBundle\Entity\Task $task = null)
    {
        $this->task = $task;

        return $this;
    }

    /**
     * Get task
     *
     * @return \TaskManagerBundle\Entity\Task
     */
    public function getTask()
    {
        return $this->task;
    }
}
