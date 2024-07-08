<?php

declare(strict_types=1);

namespace Stepapo\Dataset\Control\Sorting;

use Stepapo\Dataset\Control\BaseTemplate;


class SortingTemplate extends BaseTemplate
{
	public SortingControl $control;

	public bool $show;

	public ?string $sort;

	public ?string $direction;
}
