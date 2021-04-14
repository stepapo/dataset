<?php

declare(strict_types=1);

namespace Stepapo\Data\UI\Dataset\FilterList;

use Stepapo\Data\UI\Dataset\DatasetControl;
use Stepapo\Data\UI\Dataset\Filter\FilterControl;
use Nette\Application\UI\Multiplier;


/**
 * @property-read FilterListTemplate $template
 * @method onFilter(FilterListControl $control)
 */
class FilterListControl extends DatasetControl
{
	/** @persistent */
	public ?string $value = null;

	public array $onFilter = [];


	public function render()
	{
		parent::render();
		$this->template->render($this->getSelectedView()->filterListTemplate);
	}


	public function createComponentFilter()
	{
		return new Multiplier(function ($name): FilterControl {
			$control = new FilterControl(
				$this->getColumns()[$name],
			);
			$control->onFilter[] = function (FilterControl $filter) {
				$this->onFilter($this);
				$this->redrawControl();
			};
			return $control;
		});
	}
}
