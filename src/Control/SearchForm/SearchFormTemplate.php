<?php

declare(strict_types=1);

namespace Stepapo\Dataset\Control\SearchForm;

use Stepapo\Dataset\Control\BaseTemplate;


class SearchFormTemplate extends BaseTemplate
{
	public SearchFormControl $control;

	public ?string $term;
}
