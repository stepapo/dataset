<?php

declare(strict_types=1);

namespace Stepapo\Dataset\Control\Dataset;

use Stepapo\Dataset\Control\BaseTemplate;


class DatasetTemplate extends BaseTemplate
{
	public DatasetControl $control;

	public bool $showSearch;

	public int $count;

	public bool $showPagination;

	public ?string $term;

	public ?string $suggestedTerm;
}
