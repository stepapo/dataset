<?php

declare(strict_types=1);

namespace Stepapo\Dataset\Control\Filter;

use Stepapo\Dataset\Column;
use Stepapo\Dataset\Control\BaseTemplate;


class FilterTemplate extends BaseTemplate
{
	public FilterControl $control;

	public Column $column;

	public ?string $value;
}
