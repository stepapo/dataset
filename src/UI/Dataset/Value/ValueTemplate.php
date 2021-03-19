<?php

declare(strict_types=1);

namespace Stepapo\Data\UI\Dataset\Value;

use Stepapo\Data\Column;
use Stepapo\Data\UI\Dataset\DatasetControlTemplate;
use Nextras\Orm\Entity\IEntity;


class ValueTemplate extends DatasetControlTemplate
{
	public IEntity $entity;

	/** @var mixed */
	public $value;

	public ?array $linkArgs;

	public Column $column;

	public array $filter;
}
