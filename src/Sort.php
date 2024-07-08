<?php

declare(strict_types=1);

namespace Stepapo\Dataset;

use Nextras\Orm\Collection\ICollection;
use Stepapo\Utils\Attribute\Type;
use Stepapo\Utils\Schematic;


class Sort extends Schematic
{
	public bool $isDefault = false;
	public string $direction = ICollection::ASC;
	#[Type(OrmFunction::class)] public OrmFunction|array|null $function = null;
}
