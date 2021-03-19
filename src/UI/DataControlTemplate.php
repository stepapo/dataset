<?php

declare(strict_types=1);

namespace Stepapo\Data\UI;

use Stepapo\Data\Column;
use Stepapo\Data\View;
use Nette\Bridges\ApplicationLatte\Template;


abstract class DataControlTemplate extends Template
{
	/** @var Column[] */
	public array $columns;

	public View $selectedView;
}
