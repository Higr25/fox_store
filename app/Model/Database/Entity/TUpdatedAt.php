<?php declare(strict_types = 1);

namespace App\Model\Database\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

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
