<?php

declare(strict_types=1);

namespace Stepapo\Dataset\Control\Display;

use Stepapo\Dataset\Control\BaseTemplate;


class DisplayTemplate extends BaseTemplate
{
	public DisplayControl $control;

	public ?string $viewName;
}
