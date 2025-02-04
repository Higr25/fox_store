<?php declare(strict_types = 1);

namespace App\Model\Database\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

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
