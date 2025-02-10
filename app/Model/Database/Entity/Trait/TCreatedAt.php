<?php declare(strict_types = 1);

namespace App\Model\Database\Entity\Trait;

use DateTime;

trait TCreatedAt
{

	/** @ORM\Column(type="datetime", nullable=FALSE) */
	protected DateTime $created_at;

	public function getCreatedAt(): DateTime
	{
		return $this->created_at;
	}

	/**
	 * Doctrine annotation
	 *
	 * @ORM\PrePersist
	 * @internal
	 */
	public function setCreatedAt(): void
	{
		$this->created_at = new DateTime();
	}

}
