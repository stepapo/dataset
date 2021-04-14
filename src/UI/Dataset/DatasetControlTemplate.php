<?php

declare(strict_types=1);

namespace Stepapo\Data\UI\Dataset;

use Stepapo\Data\Button;
use Stepapo\Data\UI\DataControlTemplate;


abstract class DatasetControlTemplate extends DataControlTemplate
{
	/** @var Button[]|null */
	public ?array $buttons;
}
