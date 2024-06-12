<?php

declare(strict_types=1);

namespace Stepapo\Dataset\UI\Dataset;

use Stepapo\Dataset\UI\DatasetControlTemplate;


class DatasetTemplate extends DatasetControlTemplate
{
	public Dataset $control;

	public bool $showSearch;

	public int $count;

	public bool $showPagination;

	public ?string $term;

	public ?string $suggestedTerm;
}
