<?php

declare(strict_types=1);

namespace Stepapo\Dataset\UI\Display;

use Stepapo\Dataset\UI\DatasetControlTemplate;


class DisplayTemplate extends DatasetControlTemplate
{
	public DisplayControl $control;

	public ?string $viewName;
}
