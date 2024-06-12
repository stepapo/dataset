<?php

declare(strict_types=1);

namespace Stepapo\Dataset\UI\Filter;

use Nette\Application\Attributes\Persistent;
use Stepapo\Dataset\Column;
use Stepapo\Dataset\UI\DatasetControl;


/**
 * @property-read FilterTemplate $template
 * @method onFilter(FilterControl $control)
 */
class FilterControl extends DatasetControl
{
	#[Persistent]
	public ?string $value = null;

	public array $onFilter;


	public function __construct(
		private Column $column
	) {}


	public function render()
	{
		parent::render();
		$this->template->column = $this->column;
		$this->template->value = $this->value;
		$this->template->render($this->getSelectedView()->filterTemplate);
	}


	public function handleFilter($value = null): void
	{
		$this->value = $value;
		if ($this->presenter->isAjax()) {
			$this->onFilter($this);
			$this->redrawControl();
		}
	}
}
