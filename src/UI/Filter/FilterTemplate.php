<?php

declare(strict_types=1);

namespace Stepapo\Dataset\UI\Filter;

use Stepapo\Dataset\Column;
use Stepapo\Dataset\UI\DatasetControlTemplate;


class FilterTemplate extends DatasetControlTemplate
{
	public FilterControl $control;

	public Column $column;

	public ?string $value;
}
