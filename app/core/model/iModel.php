<?php
/**
 * This file is part of reliadmin project.
 * @author Stanislav Opletal <info@relisoft.cz>
 */

namespace App\Core\Model;


use Nette\Database\Table\ActiveRow;

interface iModel extends \JsonSerializable
{
    public function getId(): ?int;
    public function table(): string;
    public function associate(ActiveRow $row): self;
}