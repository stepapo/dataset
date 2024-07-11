<?php

declare(strict_types=1);

namespace Stepapo\Dataset\Control\Sorting;

use Nette\Application\Attributes\Persistent;
use Nextras\Orm\Collection\ICollection;
use Stepapo\Data\Control\DataControl;
use Stepapo\Data\Control\MainComponent;
use Stepapo\Data\Text;
use Stepapo\Dataset\Control\Dataset\DatasetControl;


/**
 * @property-read SortingTemplate $template
 */
class SortingControl extends DataControl
{
	#[Persistent] public ?string $sort = null;
	#[Persistent] public ?string $direction = ICollection::ASC;
	/** @var callable[] */ public array $onSort;


	public function __construct(
		private DatasetControl $main,
		private array $columns,
		private Text $text,
	) {}


	public function render(): void
	{
		$this->template->show = false;
		$sortCount = 0;
		foreach ($this->columns as $column) {
			if ($column->sort) {
				$sortCount++;
 				if ($sortCount > 1) {
					$this->template->show = true;
					break;
				}
			}
		}
		$this->template->columns = $this->columns;
		$this->template->sort = $this->sort;
		$this->template->direction = $this->direction;
		$this->template->text = $this->text;
		$this->template->render($this->main->getView()->sortingTemplate);
	}


	public function handleSort(?string $sort = null, ?string $direction = ICollection::ASC): void
	{
		$this->sort = $sort;
		$this->direction = $direction;
		if ($this->presenter->isAjax()) {
			$this->onSort($this);
			$this->redrawControl();
		}
	}
}
