<?php

declare(strict_types=1);

namespace Stepapo\Data\UI\Dataset\Filtering;

use Stepapo\Data\UI\Dataset\DatasetControl;
use Stepapo\Data\UI\Dataset\Filter\Filter;
use Nette\Application\UI\Multiplier;


/**
 * @property-read FilteringTemplate $template
 * @method onFilter(SimpleFiltering $control)
 */
class SimpleFiltering extends DatasetControl implements Filtering
{
	/** @persistent */
	public ?string $value = null;

	public array $onFilter = [];


	public function render()
	{
		parent::render();
		$this->template->render($this->getSelectedView()->filteringTemplate);
	}


	public function createComponentFilter()
	{
		return new Multiplier(function ($name): Filter {
			$control = $this->getFactory()->createFilter(
				$this->getColumns()[$name],
			);
			$control->onFilter[] = function (Filter $filter) {
				$this->onFilter($this);
				$this->redrawControl();
			};
			return $control;
		});
	}
}
