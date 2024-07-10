<?php

declare(strict_types=1);

namespace Stepapo\Dataset\Control\SearchForm;

use Stepapo\Data\Control\DataTemplate;


class SearchFormTemplate extends DataTemplate
{
	public SearchFormControl $control;

	public ?string $term;

	public string $placeholder;
}
