<?php
/**
 * This file is part of reliadmin project.
 * @author Stanislav Opletal <info@relisoft.cz>
 */

namespace App\Core\Model;


use Nette\Database\Table\ActiveRow;
use Nette\Utils\DateTime;

class Post implements iModel
{
    /** @var int|null */
    private $id;
    /** @var User\User|int|null */
    private $author;
    /** @var string|null */
    private $content;
    /** @var DateTime|null */
    private $create_date;
    /** @var DateTime|null */
    private $update_date;
    /** @var int|null */
    private $response_to;

    public function table(): string {
        return 'post';
    }

    public function associate(ActiveRow $row): iModel
    {
        $this->setId($row->id ?? null);
        $this->setAuthor((new User\User())->associate($row->ref('author')) ?? null);
        $this->setContent($row->content ?? null);
        $this->setCreateDate($row->create_date ?? null);
        $this->setUpdateDate($row->update_date ?? null);
        $this->setResponseTo($row->response_to ?? null);
        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'author' => $this->getAuthor(),
            'content' => $this->getContent(),
            'create_date' => $this->getCreateDate(),
            'update_date' => $this->getUpdateDate(),
            'response_to' => $this->getResponseTo()
        ];
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return Post
     */
    public function setId(?int $id): Post
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getResponseTo(): ?int
    {
        return $this->response_to;
    }

    /**
     * @param int|null $response_to
     */
    public function setResponseTo(?int $response_to): void
    {
        $this->response_to = $response_to;
    }

    /**
     * @return User\User|int|null
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param User\User|int|null $author
     */
    public function setAuthor($author): void
    {
        $this->author = $author;
    }

    /**
     * @return null|string
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @param null|string $content
     * @return Post
     */
    public function setContent(?string $content): Post
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getCreateDate(): ?DateTime
    {
        return $this->create_date;
    }

    /**
     * @param DateTime|null $create_date
     * @return Post
     */
    public function setCreateDate(?DateTime $create_date): Post
    {
        $this->create_date = $create_date;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getUpdateDate(): ?DateTime
    {
        return $this->update_date;
    }

    /**
     * @param DateTime|null $update_date
     * @return Post
     */
    public function setUpdateDate(?DateTime $update_date): Post
    {
        $this->update_date = $update_date;
        return $this;
    }



}