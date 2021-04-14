<?php

declare(strict_types=1);

namespace Stepapo\Data\UI;

use Nette\Application\UI\Control;
use Stepapo\Data\Column;
use Stepapo\Data\View;
use Nette\Bridges\ApplicationLatte\Template;


abstract class DataControlTemplate extends Template
{
	/** @var Column[] */
	public array $columns;

	/** @var View[]|null */
	public ?array $views;

	public View $selectedView;
}
