<?php

declare(strict_types=1);

namespace Stepapo\Dataset\Control\Dataset;

use Stepapo\Data\Control\DataTemplate;


class DatasetTemplate extends DataTemplate
{
	public DatasetControl $control;
	public bool $showSearch;
	public bool $showFilter;
	public int $count;
	public bool $showPagination;
	public bool $isResponsive;
	public ?string $term;
	public ?string $suggestedTerm;
	public ?string $datasetClass;
}
