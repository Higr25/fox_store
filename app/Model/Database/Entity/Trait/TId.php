<?php declare(strict_types = 1);

namespace App\Model\Database\Entity\Trait;

trait TId
{

	/**
	 * @ORM\Column(type="integer", nullable=FALSE)
	 * @ORM\Id
	 * @ORM\GeneratedValue
	 */
	private int $id;

	public function getId(): int
	{
		return $this->id;
	}

	public function __clone()
	{
		unset($this->id);
	}

}
