<?php

declare(strict_types=1);

namespace Stepapo\Dataset\UI\Value;

use Stepapo\Dataset\Column;
use Stepapo\Dataset\UI\DatasetControlTemplate;
use Nextras\Orm\Entity\IEntity;


class ValueTemplate extends DatasetControlTemplate
{
	public IEntity $entity;

	public mixed $value;

	public ?array $linkArgs;

	public Column $column;
}
