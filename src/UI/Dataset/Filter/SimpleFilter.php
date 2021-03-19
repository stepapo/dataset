<?php

declare(strict_types=1);

namespace Stepapo\Data\UI\Dataset\Filter;

use Stepapo\Data\Column;
use Stepapo\Data\UI\Dataset\DatasetControl;


/**
 * @property-read FilterTemplate $template
 * @method onFilter(SimpleFilter $control)
 */
class SimpleFilter extends DatasetControl implements Filter
{
	/** @persistent */
	public ?string $value = null;

	public array $onFilter = [];

	private Column $column;


	public function __construct(
		Column $column
	) {
		$this->column = $column;
	}


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
