<?php

declare(strict_types=1);

namespace Stepapo\Dataset\Control\Dataset;

use Contributte\ImageStorage\ImageStorage;
use Nette\Localization\Translator;
use Nette\Utils\Paginator;
use Nextras\Orm\Collection\ICollection;
use Nextras\Orm\Entity\IEntity;
use Stepapo\Dataset\Control\BaseControl;
use Stepapo\Dataset\Control\Display\DisplayControl;
use Stepapo\Dataset\Control\FilterList\FilterListControl;
use Stepapo\Dataset\Control\ItemList\ItemListControl;
use Stepapo\Dataset\Control\Pagination\PaginationControl;
use Stepapo\Dataset\Control\SearchForm\SearchFormControl;
use Stepapo\Dataset\Control\Sorting\SortingControl;
use Stepapo\Dataset\Dataset;
use Stepapo\Dataset\Option;
use Stepapo\Dataset\Text;
use Stepapo\Dataset\View;


/**
 * @property-read DatasetTemplate $template
 * @method onItemChange(DatasetControl $control, ?IEntity $entity = null)
 */
class DatasetControl extends BaseControl
{
	/** @var callable[] */ public array $onItemChange;
	private View $selectedView;
	private ICollection $items;
	private int $currentCount;
	private int $totalCount;
	public bool $shouldRetrieveItems = true;


	public function __construct(
		private Dataset $dataset,
	) {}


	public function loadState(array $params): void
	{
		parent::loadState($params);
		$this->selectedView = $this->selectView();
	}


	public function getCollectionItems(): ICollection
	{
		if (isset($this->items)) {
			return $this->items;
		}
		$c = $this->dataset->collection;
		$c = $this->filter($c);
		$c = $this->search($c);
		$c = $this->sort($c);
		$c = $this->paginate($c);
		$this->currentCount = $c->count();
		$this->items = $c;
		return $this->items;
	}


	public function getCurrentCount(): int
	{
		if (isset($this->currentCount)) {
			return $this->currentCount;
		}
		$this->currentCount = $this->getCollectionItems()->count();
		return $this->currentCount;
	}


	public function getText(): Text
	{
		return $this->dataset->text;
	}


	public function getColumns(): array
	{
		return $this->dataset->columns;
	}


	public function getViews(): array
	{
		return $this->dataset->views;
	}


	public function getTranslator(): Translator
	{
		return $this->dataset->translator;
	}


	public function getImageStorage(): ?ImageStorage
	{
		return $this->dataset->imageStorage;
	}


	public function getSelectedView(): View
	{
		return $this->selectedView;
	}


	public function getCollectionCount(): int
	{
		if (isset($this->totalCount)) {
			return $this->totalCount;
		}
		$c = $this->dataset->collection;
		$c = $this->filter($c);
		$c = $this->search($c);
		$this->totalCount = $c->countStored();
		return $this->totalCount;
	}


	public function render()
	{
		parent::render();
		$this->template->showPagination = (bool) $this->dataset->itemsPerPage;
		$this->template->showSearch = (bool) $this->dataset->search;
		if ($this->dataset->itemsPerPage && $this->shouldRetrieveItems) {
			$count = $this->getCurrentCount();
			$term = $this->dataset->search ? $this->getComponent('searchForm')->term : null;
			$this->template->term = $term;
			if ($count == 0 && $term && $this->dataset->search->suggestCallback) {
				$this->template->suggestedTerm = ($this->dataset->search->suggestCallback)($term);
			}
		}
		$this->template->render($this->selectedView->datasetTemplate);
	}


	public function createComponentItemList(): ItemListControl
	{
		return new ItemListControl(
			$this->dataset->idColumnName,
			$this->dataset->itemsPerPage,
			$this->dataset->itemListClass,
			$this->dataset->itemClassCallback,
			$this->dataset->itemLink
		);
	}


	public function createComponentPagination(): PaginationControl
	{
		$pagination = new PaginationControl(
			(new Paginator)
				->setItemsPerPage($this->dataset->itemsPerPage),
		);
		$pagination->onPaginate[] = function (PaginationControl $pagination) {
			$this->getComponent('itemList')->redrawControl();
		};
		return $pagination;
	}


	public function createComponentFilterList(): FilterListControl
	{
		$control = new FilterListControl($this->dataset->columns);
		$control->onFilter[] = function (FilterListControl $filtering) {
			$this->redrawControl();
		};
		return $control;
	}


	public function createComponentSearchForm(): SearchFormControl
	{
		$control = new SearchFormControl($this->dataset->search->placeholder);
		$control->onSearch[] = function (SearchFormControl $search) {
			$this->redrawControl();
		};
		return $control;
	}


	public function createComponentSorting(): SortingControl
	{
		$control = new SortingControl();
		$control->onSort[] = function (SortingControl $sorting) {
			$this->redrawControl();
		};
		return $control;
	}


	public function createComponentDisplay(): DisplayControl
	{
		$control = new DisplayControl();
		$control->onDisplay[] = function (DisplayControl $display) {
			$this->redrawControl();
		};
		return $control;
	}


	private function filter(ICollection $c): ICollection
	{
		foreach ($this->dataset->columns as $column) {
			if (!$column->filter) {
				continue;
			}
			$value = $this->getComponent('filterList')->getComponent('filter')->getComponent($column->name)->value;
			if (!$value) {
				continue;
			}
			if ($column->filter->options[$value] instanceof Option && $column->filter->options[$value]->condition) {
				$c = $c->findBy($column->filter->options[$value]->condition);
			} elseif ($column->filter->function) {
				$c = $c->findBy([$column->filter->function, $column->getNextrasName(), $value]);
			} else {
				$c = $c->findBy([$column->filter->columnName ? $column->filter->getNextrasName() : $column->name => $value]);
			}
		}
		return $c;
	}


	private function search(ICollection $c): ICollection
	{
		if (!$this->dataset->search || !($term = $this->getComponent('searchForm')->term)) {
			return $c;
		}
		if ($this->dataset->search->prepareCallback and is_callable($this->dataset->search->prepareCallback)) {
			$term = ($this->dataset->search->prepareCallback)($term);
		}
		array_walk($this->dataset->search->searchFunction->args, fn(&$v) => $v = $v == '%term%' ? $term : $v);
		$c = $c->findBy(array_merge([$this->dataset->search->searchFunction->class], $this->dataset->search->searchFunction->args));
		if ($this->dataset->search->sortFunction) {
			array_walk($this->dataset->search->sortFunction->args, fn(&$v) => $v = $v == '%term%' ? $term : $v);
		}
		return $c;
	}


	private function sort(ICollection $c): ICollection
	{
		$sort = $this->getComponent('sorting')->sort;
		if ($sort) {
			$column = $this->dataset->columns[$sort];
			$direction = $this->getComponent('sorting')->direction;
			if ($column->sort->function) {
				$c = $c->orderBy(array_merge([$column->sort->function->class], (array) $column->sort->function->args), $direction);
			} else {
				$c = $c->orderBy($column->getNextrasName(), $direction);
			}
		}
		foreach ($this->dataset->columns as $column) {
			if (!$column->sort || !$column->sort->isDefault || $column->name == $sort) {
				continue;
			}
			if ($column->sort->function) {
				$c = $c->orderBy(array_merge([$column->sort->function->class], (array) $column->sort->function->args), $column->sort->direction);
			} else {
				$c = $c->orderBy($column->getNextrasName(), $column->sort->direction);
			}
		}

		if (
			$this->dataset->search
			&& $this->dataset->search->sortFunction
			&& $this->getComponent('searchForm')->term
			&& ($this->getComponent('searchForm-form')->isSubmitted() || !$sort)
		) {
			$c = $c->orderBy(array_merge([$this->dataset->search->sortFunction->class], (array) $this->dataset->search->sortFunction->args), $this->dataset->search->sortDirection);
		}
		$primaryKey = $this->dataset->repository->getEntityMetadata()->getPrimaryKey();
		return count($primaryKey) === 1 ? $c->orderBy($primaryKey[0]) : $c;
	}


	private function paginate(ICollection $c): ICollection
	{
		if ($this->dataset->itemsPerPage) {
			$c = $c->limitBy(
				$this->getComponent('pagination')->getPaginator()->length + 1,
				$this->getComponent('pagination')->getPaginator()->offset
			);
		}
		return $c;
	}


	private function selectView(): View
	{
		if ($viewName = $this->getComponent('display')->viewName) {
			if (isset($this->dataset->views[$viewName])) {
				return $this->dataset->views[$viewName];
			}
		}

		foreach ($this->dataset->views as $view) {
			if ($view->isDefault) {
				return $view;
			}
		}
		return array_values($this->dataset->views)[0];
	}
}
