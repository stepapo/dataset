<?php

declare(strict_types=1);

namespace Stepapo\Dataset\UI\SearchForm;

use Stepapo\Dataset\UI\DatasetControlTemplate;


class SearchFormTemplate extends DatasetControlTemplate
{
	public SearchFormControl $control;

	public ?string $term;
}
