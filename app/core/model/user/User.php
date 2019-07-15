<?php
/**
 * This file is part of reliadmin project.
 * @author Stanislav Opletal <info@relisoft.cz>
 */

namespace App\Core\Model\User;


use App\Core\Model\iModel;
use Nette\Database\Table\ActiveRow;
use Nette\Utils\DateTime;

class User implements iModel
{
    const ROLE_GUEST = 'GUEST', ROLE_USER = 'USER', ROLE_MOD = 'MOD', ROLE_ADMIN = 'ADMIN', ROLE_OWNER = 'OWNER';

    /** @var int|null */
    private $id;
    /** @var string|null */
    private $name;
    /** @var string|null */
    private $surname;
    /** @var string|null */
    private $email;
    /** @var string|null */
    private $password;
    /** @var string|null */
    private $role;
    /** @var DateTime|null */
    private $create_date;
    /** @var DateTime|null */
    private $update_date;
    /** @var string|null */
    private $motto;

    public function table(): string
    {
        return 'user';
    }

    public function associate(ActiveRow $row): iModel
    {
        $this->setId($row->id ?? null);
        $this->setName($row->name ?? null);
        $this->setSurname($row->surname ?? null);
        $this->setEmail($row->email ?? null);
        $this->setPassword($row->password ?? null);
        $this->setRole($row->role ?? null);
        $this->setCreateDate($row->create_date ?? null);
        $this->setUpdateDate($row->update_date ?? null);
        $this->setMotto($row->motto ?? null);
        return $this;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'surname' => $this->getSurname(),
            'email' => $this->getEmail(),
            'password' => $this->getPassword(),
            'role' => $this->getRole(),
            'create_date' => $this->getCreateDate(),
            'update_date' => $this->getUpdateDate(),
            'motto' => $this->getMotto(),
        ];
    }

    /**
     * @return null|string
     */
    public function getMotto(): ?string
    {
        return $this->motto;
    }

    /**
     * @param null|string $motto
     * @return User
     */
    public function setMotto(?string $motto): User
    {
        $this->motto = $motto;
        return $this;
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
     * @return User
     */
    public function setId(?int $id): User
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param null|string $name
     * @return User
     */
    public function setName(?string $name): User
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getSurname(): ?string
    {
        return $this->surname;
    }

    /**
     * @param null|string $surname
     * @return User
     */
    public function setSurname(?string $surname): User
    {
        $this->surname = $surname;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param null|string $email
     * @return User
     */
    public function setEmail(?string $email): User
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param null|string $password
     * @return User
     */
    public function setPassword(?string $password): User
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getRole(): ?string
    {
        return $this->role;
    }

    /**
     * @param null|string $role
     * @return User
     */
    public function setRole(?string $role): User
    {
        $this->role = $role;
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
     * @return User
     */
    public function setCreateDate(?DateTime $create_date): User
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
     * @return User
     */
    public function setUpdateDate(?DateTime $update_date): User
    {
        $this->update_date = $update_date;
        return $this;
    }
}