<?php

declare(strict_types=1);

namespace Stepapo\Dataset\Control\FilterList;

use Nette\Application\Attributes\Persistent;
use Stepapo\Dataset\Control\BaseControl;
use Stepapo\Dataset\Control\Filter\FilterControl;
use Nette\Application\UI\Multiplier;


/**
 * @property-read FilterListTemplate $template
 * @method onFilter(FilterListControl $control)
 */
class FilterListControl extends BaseControl
{
	#[Persistent]
	public ?string $value = null;

	public array $onFilter;


	public function __construct(
		private array $columns,
	) {}


	public function render()
	{
		parent::render();
		$this->template->render($this->getSelectedView()->filterListTemplate);
	}


	public function createComponentFilter()
	{
		return new Multiplier(function ($name): FilterControl {
			$control = new FilterControl(
				$this->columns[$name],
			);
			$control->onFilter[] = function (FilterControl $filter) {
				$this->onFilter($this);
				$this->redrawControl();
			};
			return $control;
		});
	}
}
