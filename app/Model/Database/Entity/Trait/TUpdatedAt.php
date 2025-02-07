<?php declare(strict_types = 1);

namespace App\Model\Database\Entity\Trait;

use DateTime;

trait TUpdatedAt
{

	/** @ORM\Column(type="datetime", nullable=FALSE) */
	protected DateTime $updated_at;

	public function getUpdatedAt(): DateTime
	{
		return $this->updated_at;
	}

	/**
	 * Doctrine annotation
	 *
	 * @ORM\PrePersist
	 * @internal
	 */
	public function setUpdatedAt(): void
	{
		$this->updated_at = new DateTime();
	}

}
