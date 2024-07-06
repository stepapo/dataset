<?php

declare(strict_types=1);

namespace Stepapo\Dataset;

use Nextras\Orm\Collection\ICollection;
use Stepapo\Utils\Attribute\Type;
use Stepapo\Utils\Schematic;


class Search extends Schematic
{
	public ?string $placeholder = null;
	public $prepareCallback = null;
	public $suggestCallback = null;
	public string $sortDirection = ICollection::ASC;
	#[Type(OrmFunction::class)] public OrmFunction|array $searchFunction;
	#[Type(OrmFunction::class)] public OrmFunction|array|null $sortFunction = null;
}
