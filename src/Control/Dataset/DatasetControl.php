<?php

declare(strict_types=1);

namespace Stepapo\Dataset\Control\Dataset;

use Nette\Application\BadRequestException;
use Nette\InvalidArgumentException;
use Nette\NotSupportedException;
use Nette\Utils\Paginator;
use Nette\Utils\Random;
use Nextras\Orm\Collection\Aggregations\AnyAggregator;
use Nextras\Orm\Collection\Aggregations\NoneAggregator;
use Nextras\Orm\Collection\ICollection;
use Nextras\Orm\Entity\IEntity;
use Stepapo\Data\Control\DataControl;
use Stepapo\Data\Control\FilterList\FilterListControl;
use Stepapo\Data\Control\MainComponent;
use Stepapo\Data\Helper;
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
	public bool $activeFilter = false;


	public function __construct(
		private Dataset $dataset,
	) {}


	public function getCollection(): ICollection
	{
		return $this->dataset->collection;
	}


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
			$this->dataset->pagingMode,
		);
	}


	public function createComponentPagination(): PaginationControl
	{
		$pagination = new PaginationControl(
			$this,
			(new Paginator)->setItemsPerPage($this->dataset->itemsPerPage),
			$this->dataset->text,
			$this->dataset->hidePagination,
			$this->dataset->pagingMode,
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
		$control = new SearchFormControl($this, $this->dataset->text, $this->dataset->search->placeholder);
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
			$this->activeFilter = true;
			if ($column->filter->type === 'single') {
				if ($column->filter->options[$value] instanceof Option && $column->filter->options[$value]->condition) {
					$c = $c->findBy($column->filter->options[$value]->condition);
				} elseif ($column->filter->function) {
					if (is_array($column->filter->columnName)) {
						$filter = [ICollection::OR];
						foreach ($column->filter->columnName as $columnName) {
							$filter[] = [$column->filter->function, Helper::getNextrasName($columnName), $value];
						}
						$c = $c->findBy($filter);
					} else {
						$c = $c->findBy([$column->filter->function, Helper::getNextrasName($column->columnName), $value]);
					}
				} else {
					if (is_array($column->filter->columnName)) {
						$filter = [ICollection::OR];
						foreach ($column->filter->columnName as $columnName) {
							$filter[] = [Helper::getNextrasName($columnName)];
						}
						$c = $c->findBy($filter);
					} else {
						$c = $c->findBy([$column->filter->columnName ? Helper::getNextrasName($column->filter->columnName) : $column->name => $value]);
					}
				}
			} else {
				$value = explode(',', $value);
				if ($column->filter->multiMode === 'any') {
					$filter = [ICollection::OR];
					foreach ($value as $v) {
						if ($column->filter->options[$v] instanceof Option && $column->filter->options[$v]->condition) {
							$filter[] = $column->filter->options[$v]->condition;
						} elseif ($column->filter->function) {
							$filter[] = [$column->filter->function, Helper::getNextrasName($column->filter->columnName), $v];
						} else {
							if (is_array($column->filter->columnName)) {
								$f = [ICollection::OR];
								foreach ($column->filter->columnName as $columnName) {
									$f[] = [Helper::getNextrasName($columnName) => $v];
								}
								$filter[] = $f;
							} else {
								$filter[] = [$column->filter->columnName ? Helper::getNextrasName($column->filter->columnName) : $column->name => $v];
							}
						}
					}
					$c = $c->findBy($filter);
				} elseif ($column->filter->multiMode === 'all') {
					$filter = [ICollection::AND];
					foreach ($value as $v) {
						$aggregator = new AnyAggregator(Random::generate());
						if ($column->filter->options[$v] instanceof Option && $column->filter->options[$v]->condition) {
							$filter[] = [ICollection::AND, $aggregator, $column->filter->options[$v]->condition];
						} elseif ($column->filter->function) {
							$filter[] = [ICollection::AND, $aggregator, [$column->filter->function, Helper::getNextrasName($column->filter->columnName), $v]];
						} else {
							if (is_array($column->filter->columnName)) {
								$f = [ICollection::OR];
								foreach ($column->filter->columnName as $columnName) {
									$f[] = [Helper::getNextrasName($columnName) => $v];
								}
								$filter[] = [ICollection::AND, $aggregator, $f];
							} else {
								$filter[] = [ICollection::AND, $aggregator, $column->filter->columnName ? Helper::getNextrasName($column->filter->columnName) : $column->name => $v];
							}
						}
					}
					$c = $c->findBy($filter);
				} elseif ($column->filter->multiMode === 'none') {
					throw new NotSupportedException;
				} else {
					throw new InvalidArgumentException;
				}
			}
		}
		return $c;
	}


	private function search(ICollection $c): ICollection
	{
		if (!$this->dataset->search || !($term = $this->getComponent('searchForm')->term)) {
			return $c;
		}
		$this->activeFilter = true;
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
			if (!$column->sort) {
				return $c;
			}
			$direction = $this->getComponent('sorting')->direction;
			if ($column->sort->function) {
				$c = $c->orderBy(array_merge([$column->sort->function->class], (array) $column->sort->function->args), strtoupper($direction));
			} else {
				$c = $c->orderBy(Helper::getNextrasName($column->columnName), strtoupper($direction));
			}
		}
		foreach ($this->dataset->columns as $column) {
			if (!$column->sort || !$column->sort->isDefault || $column->name == $sort) {
				continue;
			}
			if ($column->sort->function) {
				$c = $c->orderBy(array_merge([$column->sort->function->class], (array) $column->sort->function->args), strtoupper($column->sort->direction));
			} else {
				$c = $c->orderBy(Helper::getNextrasName($column->columnName), strtoupper($column->sort->direction));
			}
		}

		if (
			$this->dataset->search
			&& $this->dataset->search->sortFunction
			&& $this->getComponent('searchForm')->term
			&& ($this->getComponent('searchForm-form')->isSubmitted() || !$sort)
		) {
			$c = $c->orderBy(array_merge([$this->dataset->search->sortFunction->class], (array) $this->dataset->search->sortFunction->args), strtoupper($this->dataset->search->sortDirection));
		}
		$primaryKey = $this->dataset->repository->getEntityMetadata()->getPrimaryKey();
		return count($primaryKey) === 1 ? $c->orderBy($primaryKey[0]) : $c;
	}


	private function paginate(ICollection $c): ICollection
	{
		if ($this->dataset->itemsPerPage) {
			$c = $c->limitBy(
				$this->getComponent('pagination')->getPaginator()->length + 1
				+ (
				$this->dataset->pagingMode === 'fromStart'
					? $this->getComponent('pagination')->getPaginator()->offset
					: 0
				),
				$this->dataset->pagingMode === 'fromPreviousPage'
					? $this->getComponent('pagination')->getPaginator()->offset
					: 0,
			);
		}
		return $c;
	}
}
