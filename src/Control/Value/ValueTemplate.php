<?php

declare(strict_types=1);

namespace Stepapo\Dataset\Control\Value;

use Nextras\Orm\Entity\IEntity;
use Stepapo\Dataset\Column;
use Stepapo\Dataset\Control\BaseTemplate;


class ValueTemplate extends BaseTemplate
{
	public IEntity $entity;

	public mixed $value;

	public ?array $linkArgs;

	public Column $column;
}
