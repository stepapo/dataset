<?php

declare(strict_types=1);

namespace Stepapo\Dataset\UI\Sorting;

use Stepapo\Dataset\UI\DatasetControlTemplate;


class SortingTemplate extends DatasetControlTemplate
{
	public SortingControl $control;

	public bool $show;

	public ?string $sort;

	public ?string $direction;
}
