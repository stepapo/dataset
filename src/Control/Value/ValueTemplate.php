<?php

declare(strict_types=1);

namespace Stepapo\Dataset\Control\Value;

use Stepapo\Dataset\Column;
use Stepapo\Dataset\Control\BaseTemplate;
use Nextras\Orm\Entity\IEntity;


class ValueTemplate extends BaseTemplate
{
	public IEntity $entity;

	public mixed $value;

	public ?array $linkArgs;

	public Column $column;
}
