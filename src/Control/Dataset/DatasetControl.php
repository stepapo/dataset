<?php

declare(strict_types=1);

namespace Stepapo\Dataset\Control\Dataset;

use Nette\Utils\Paginator;
use Nextras\Orm\Collection\ICollection;
use Nextras\Orm\Entity\IEntity;
use Stepapo\Data\Control\DataControl;
use Stepapo\Data\Control\FilterList\FilterListControl;
use Stepapo\Data\Control\MainComponent;
use Stepapo\Data\Option;
use Stepapo\Dataset\Control\Display\DisplayControl;
use Stepapo\Dataset\Control\ItemList\ItemListControl;
use Stepapo\Dataset\Control\Pagination\PaginationControl;
use Stepapo\Dataset\Control\SearchForm\SearchFormControl;
use Stepapo\Dataset\Control\Sorting\SortingControl;
use Stepapo\Dataset\Dataset;
use Stepapo\Dataset\DatasetView;


/**
 * @property-read DatasetTemplate $template
 * @method onItemChange(DatasetControl $control, ?IEntity $entity = null)
 */
class DatasetControl extends DataControl implements MainComponent
{
	/** @var \Closure[] */ public array $onItemChange;
	private DatasetView $view;
	private ICollection $items;
	private int $currentCount;
	private int $totalCount;
	public bool $shouldRetrieveItems = true;


	public function __construct(
		private Dataset $dataset,
	) {}


	public function getCollectionItems(): ICollection
	{
		if (!isset($this->items)) {
			$c = $this->dataset->collection;
			$c = $this->filter($c);
			$c = $this->search($c);
			$c = $this->sort($c);
			$c = $this->paginate($c);
			$this->currentCount = $c->count();
			$this->items = $c;
		}
		return $this->items;
	}


	public function getCurrentCount(): int
	{
		if (!isset($this->currentCount)) {
			$this->currentCount = $this->getCollectionItems()->count();
		}
		return $this->currentCount;
	}


	public function getView(): DatasetView
	{
		if (!isset($this->view)) {
			if ($viewName = $this->getComponent('display')->viewName) {
				if (isset($this->dataset->views[$viewName])) {
					$this->view = $this->dataset->views[$viewName];
					return $this->view;
				}
			} else {
				foreach ($this->dataset->views as $view) {
					if ($view->isDefault) {
						$this->view = $view;
						return $this->view;
					}
				}
				$this->view = array_values($this->dataset->views)[0];
				return $this->view;
			}
		}
		return $this->view;
	}


	public function getCollectionCount(): int
	{
		if (!isset($this->totalCount)) {
			$c = $this->dataset->collection;
			$c = $this->filter($c);
			$c = $this->search($c);
			$this->totalCount = $c->countStored();
		}
		return $this->totalCount;
	}


	public function render(): void
	{
		$this->template->showPagination = (bool) $this->dataset->itemsPerPage;
		$this->template->isResponsive = $this->dataset->isResponsive;
		$this->template->showSearch = (bool) $this->dataset->search && !$this->dataset->search->hide;
		if ($this->dataset->itemsPerPage && $this->shouldRetrieveItems) {
			$count = $this->getCurrentCount();
			$term = $this->dataset->search ? $this->getComponent('searchForm')->term : null;
			$this->template->term = $term;
			if ($count == 0 && $term && $this->dataset->search->suggestCallback) {
				$this->template->suggestedTerm = ($this->dataset->search->suggestCallback)($term);
			}
		}
		$this->template->text = $this->dataset->text;
		$this->template->render($this->getView()->datasetTemplate);
	}


	public function createComponentItemList(): ItemListControl
	{
		return new ItemListControl(
			$this,
			$this->dataset->columns,
			$this->dataset->idColumnName,
			$this->dataset->itemsPerPage,
			$this->dataset->itemListClass,
			$this->dataset->itemClassCallback,
			$this->dataset->itemLinkCallback,
			$this->dataset->alwaysRetrieveItems,
			$this->dataset->repository,
			$this->dataset->text,
		);
	}


	public function createComponentPagination(): PaginationControl
	{
		$pagination = new PaginationControl(
			$this,
			(new Paginator)->setItemsPerPage($this->dataset->itemsPerPage),
			$this->dataset->text,
			$this->dataset->hidePagination,
		);
		$pagination->onPaginate[] = function (PaginationControl $pagination) {
			$this->getComponent('itemList')->redrawControl();
		};
		return $pagination;
	}


	public function createComponentFilterList(): FilterListControl
	{
		$control = new FilterListControl($this, $this->dataset->columns);
		$control->onFilter[] = function (FilterListControl $filterList) {
			$this->redrawControl();
		};
		return $control;
	}


	public function createComponentSearchForm(): SearchFormControl
	{
		$control = new SearchFormControl($this, $this->dataset->search->placeholder, $this->dataset->text);
		$control->onSearch[] = function (SearchFormControl $search) {
			$this->redrawControl();
		};
		return $control;
	}


	public function createComponentSorting(): SortingControl
	{
		$control = new SortingControl($this, $this->dataset->columns, $this->dataset->text);
		$control->onSort[] = function (SortingControl $sorting) {
			$this->redrawControl();
		};
		return $control;
	}


	public function createComponentDisplay(): DisplayControl
	{
		$control = new DisplayControl(
			$this,
			$this->dataset->views,
			$this->dataset->text,
		);
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
		if ($this->dataset->search->searchFunction) {
			array_walk($this->dataset->search->searchFunction->args, fn(&$v) => $v = $v == '%term%' ? $term : $v);
			$c = $c->findBy(array_merge([$this->dataset->search->searchFunction->class], $this->dataset->search->searchFunction->args));			
		} elseif ($this->dataset->search->searchCallback) {
			$c = ($this->dataset->search->searchCallback)($c, $term);
		}
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
				$c = $c->orderBy(array_merge([$column->sort->function->class], (array) $column->sort->function->args), strtoupper($direction));
			} else {
				$c = $c->orderBy($column->getNextrasName(), strtoupper($direction));
			}
		}
		foreach ($this->dataset->columns as $column) {
			if (!$column->sort || !$column->sort->isDefault || $column->name == $sort) {
				continue;
			}
			if ($column->sort->function) {
				$c = $c->orderBy(array_merge([$column->sort->function->class], (array) $column->sort->function->args), strtoupper($column->sort->direction));
			} else {
				$c = $c->orderBy($column->getNextrasName(), strtoupper($column->sort->direction));
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
}
