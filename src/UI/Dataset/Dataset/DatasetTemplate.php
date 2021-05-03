<?php

declare(strict_types=1);

namespace Stepapo\Data\UI\Dataset\Dataset;

use Stepapo\Data\Part;
use Stepapo\Data\UI\Dataset\DatasetControlTemplate;


class DatasetTemplate extends DatasetControlTemplate
{
	public bool $showSearch;

	public int $count;

	/** @var Part[]|null  */
	public ?array $parts;

	public bool $showPagination;

	public ?string $term;

	public ?string $suggestedTerm;
}
