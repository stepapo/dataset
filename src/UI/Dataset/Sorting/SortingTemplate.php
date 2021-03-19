<?php

declare(strict_types=1);

namespace Stepapo\Data\UI\Dataset\Sorting;

use Stepapo\Data\UI\Dataset\DatasetControlTemplate;


class SortingTemplate extends DatasetControlTemplate
{
	public bool $show;

	public ?string $sort;

	public ?string $direction;
}
