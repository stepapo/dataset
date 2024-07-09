<?php

declare(strict_types=1);

namespace Stepapo\Dataset\Control\Sorting;

use Stepapo\Data\Control\DataTemplate;
use Stepapo\Data\Text;


class SortingTemplate extends DataTemplate
{
	public SortingControl $control;

	public bool $show;

	public ?string $sort;

	public ?string $direction;

	public Text $text;

	public array $columns;
}
