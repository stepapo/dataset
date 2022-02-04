<?php

declare(strict_types=1);

namespace Stepapo\Dataset\UI\Dataset;

use Nette\Localization\Translator;
use Nextras\Orm\Entity\IEntity;
use Nextras\Orm\Repository\IRepository;
use Stepapo\Dataset\Column;
use Stepapo\Dataset\Filter;
use Stepapo\Dataset\LatteFilter;
use Stepapo\Dataset\Link;
use Stepapo\Dataset\Option;
use Stepapo\Dataset\OrmFunction;
use Stepapo\Dataset\Sort;
use Stepapo\Dataset\Search;
use Stepapo\Dataset\Text;
use Stepapo\Dataset\UI\DatasetControl;
use Stepapo\Dataset\UI\Display\DisplayControl;
use Stepapo\Dataset\UI\FilterList\FilterListControl;
use Stepapo\Dataset\UI\ItemList\ItemListControl;
use Stepapo\Dataset\UI\Pagination\PaginationControl;
use Stepapo\Dataset\UI\SearchForm\SearchFormControl;
use Stepapo\Dataset\UI\Sorting\SortingControl;
use Stepapo\Dataset\Utils;
use Nette\InvalidArgumentException;
use Nette\Neon\Neon;
use Nette\Utils\FileSystem;
use Nette\Utils\Paginator;
use Nextras\Orm\Collection\ICollection;
use Stepapo\Dataset\View;
use Ublaboo\ImageStorage\ImageStorage;


/**
 * @property-read DatasetTemplate $template
 * @method onItemChange(Dataset $control, ?IEntity $entity = null)
 */
class Dataset extends DatasetControl
{
	/** @var callable[] */
	public array $onItemChange;

	private View $selectedView;

	private int $count;

	public bool $shouldRetrieveItems = true;


	/**
	 * @param Column[]|null $columns
	 * @param View[]|null $views
	 */
	public function __construct(
		private ICollection $collection,
		private IRepository $repository,
		private Text $text,
		private ?IEntity $parentEntity = null,
		private ?Translator $translator = null,
		private ?ImageStorage $imageStorage = null,
		private array $columns = [],
		private array $views = [],
		private ?int $itemsPerPage = null,
		private ?Search $search = null,
		private $itemClassCallback = null,
		private ?string $itemListClass = null,
		private string $idColumnName = 'id',
		private bool $alwaysRetrieveItems = false,
	) {}


	public static function createFromNeon(string $file, array $params = []): Dataset
	{
		$config = (array) Neon::decode(FileSystem::read($file));
		$parsedConfig = Utils::replaceParams($config, $params);
		return self::createFromArray($parsedConfig);
	}


	public static function createFromArray(array $config): Dataset
	{
		if (!isset($config['collection'], $config['repository'])) {
			throw new InvalidArgumentException('Dataset collection and repository has to be defined.');
		}
		$dataset = new self(
			collection: $config['collection'],
			repository: $config['repository'],
			text: Text::createFromArray($config['text'] ?? [])
		);
		if (array_key_exists('parentEntity', $config)) {
			$dataset->setParentEntity($config['parentEntity']);
		}
		if (array_key_exists('itemsPerPage', $config)) {
			$dataset->setItemsPerPage($config['itemsPerPage']);
		}
		if (array_key_exists('translator', $config)) {
			$dataset->setTranslator($config['translator']);
		}
		if (array_key_exists('imageStorage', $config)) {
			$dataset->setImageStorage($config['imageStorage']);
		}
		if (array_key_exists('search', $config)) {
			$dataset->setSearch(Search::createFromArray((array) $config['search']));
		}
		if (array_key_exists('itemClassCallback', $config)) {
			$dataset->setItemClassCallback($config['itemClassCallback']);
		}
		if (array_key_exists('itemListClass', $config)) {
			$dataset->setItemListClass($config['itemListClass']);
		}
		if (array_key_exists('idColumnName', $config)) {
			$dataset->setIdColumnName($config['idColumnName']);
		}
		if (array_key_exists('alwaysRetrieveItems', $config)) {
			$dataset->setAlwaysRetrieveItems($config['alwaysRetrieveItems']);
		}
		if (array_key_exists('columns', $config)) {
			foreach ((array) $config['columns'] as $columnName => $columnConfig) {
				$dataset->addColumn(Column::createFromArray((array) $columnConfig, $columnName));
			}
		}
		if (array_key_exists('views', $config)) {
			foreach ((array) $config['views'] as $name => $viewConfig) {
				$dataset->addView(View::createFromArray((array) $viewConfig, $name));
			}
		}
		return $dataset;
	}


	public function loadState(array $params): void
	{
		parent::loadState($params);
		if (!$this->views) {
			$this->createAndAddDefaultView();
		}
		$this->selectedView = $this->selectView();
	}


	public function getCollectionItems(): ICollection
	{
		$c = $this->getCollection();
		$c = $this->filter($c);
		$c = $this->search($c);
		$c = $this->sort($c);
		$c = $this->paginate($c);
		return $c;
	}


	public function getCollectionCount(): int
	{
		if (isset($this->count)) {
			return $this->count;
		}
		$c = $this->getCollection();
		$c = $this->filter($c);
		$c = $this->search($c);
		$this->count = $c->countStored();
		return $this->count;
	}


	public function render()
	{
		parent::render();
		$this->template->showPagination = (bool) $this->itemsPerPage;
		$this->template->showSearch = (bool) $this->search;
		if ($this->itemsPerPage && $this->shouldRetrieveItems) {
			$count = $this->getCollectionCount();
			$term = $this->search ? $this->getComponent('searchForm')->term : null;
			$this->template->count = $count;
			$this->template->term = $term;
			if ($count == 0 && $term && $this->search->suggestCallback) {
				$this->template->suggestedTerm = ($this->search->suggestCallback)($term);
			}
		}
		$this->template->render($this->selectedView->datasetTemplate);
	}


	public function getCollection(): ICollection
	{
		return $this->collection;
	}


	public function getRepository(): IRepository
	{
		return $this->repository;
	}


	public function getText(): Text
	{
		return $this->text;
	}


	public function getParentEntity(): ?IEntity
	{
		return $this->parentEntity;
	}


	public function getTranslator(): ?Translator
	{
		return $this->translator;
	}


	public function getImageStorage(): ?ImageStorage
	{
		return $this->imageStorage;
	}


	public function getItemClassCallback(): ?callable
	{
		return $this->itemClassCallback;
	}


	public function getItemListClass(): ?string
	{
		return $this->itemListClass;
	}


	public function getIdColumnName(): string
	{
		return $this->idColumnName;
	}


	/** @return Column[] */
	public function getColumns(): array
	{
		return $this->columns;
	}


	/** @return View[] */
	public function getViews(): array
	{
		return $this->views;
	}


	public function getSelectedView(): View
	{
		return $this->selectedView;
	}


	public function getItemsPerPage(): ?int
	{
		return $this->itemsPerPage;
	}


	public function getAlwaysRetrieveItems(): bool
	{
		return $this->alwaysRetrieveItems;
	}


	public function setCollection(ICollection $collection): Dataset
	{
		$this->collection = $collection;
		return $this;
	}


	public function setRepository(IRepository $repository): Dataset
	{
		$this->repository = $repository;
		return $this;
	}


	public function setText(Text $text): Dataset
	{
		$this->text = $text;
		return $this;
	}


	public function setParentEntity(?IEntity $parentEntity): Dataset
	{
		$this->parentEntity = $parentEntity;
		return $this;
	}


	public function setTranslator(?Translator $translator): Dataset
	{
		$this->translator = $translator;
		return $this;
	}


	public function setImageStorage(?ImageStorage $imageStorage): Dataset
	{
		$this->imageStorage = $imageStorage;
		return $this;
	}


	public function setItemsPerPage(?int $itemsPerPage): Dataset
	{
		$this->itemsPerPage = $itemsPerPage;
		return $this;
	}


	public function addColumn(Column $column): Dataset
	{
		$this->columns[$column->name] = $column;
		return $this;
	}


	public function createAndAddDefaultView(?string $name = null): Dataset
	{
		return $this->addView(!$name ? View::createDefault() : View::createView($name, true));
	}


	public function addView(View $view): Dataset
	{
		$this->views[$view->name] = $view;
		return $this;
	}


	public function setSearch(Search $search): Dataset
	{
		$this->search = $search;
		return $this;
	}


	public function setItemClassCallback(?callable $itemClassCallback): Dataset
	{
		$this->itemClassCallback = $itemClassCallback;
		return $this;
	}


	public function setItemListClass(?string $itemListClass): Dataset
	{
		$this->itemListClass = $itemListClass;
		return $this;
	}


	public function setIdColumnName(string $idColumnName): Dataset
	{
		$this->idColumnName = $idColumnName;
		return $this;
	}


	public function setAlwaysRetrieveItems(bool $alwaysRetrieveItems): Dataset
	{
		$this->alwaysRetrieveItems = $alwaysRetrieveItems;
		return $this;
	}


	public function createComponentItemList(): ItemListControl
	{
		return new ItemListControl();
	}


	public function createComponentPagination(): PaginationControl
	{
		$pagination = new PaginationControl(
			(new Paginator)
				->setItemsPerPage($this->itemsPerPage)
				->setItemCount($this->getCollectionCount()),
		);
		$pagination->onPaginate[] = function (PaginationControl $pagination) {
			$this->getComponent('itemList')->redrawControl();
		};
		return $pagination;
	}


	public function createComponentFilterList(): FilterListControl
	{
		$control = new FilterListControl();
		$control->onFilter[] = function (FilterListControl $filtering) {
			$this->redrawControl();
		};
		return $control;
	}


	public function createComponentSearchForm(): SearchFormControl
	{
		$control = new SearchFormControl($this->search->placeholder);
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
		foreach ($this->columns as $column) {
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
		if (!$this->search || !($term = $this->getComponent('searchForm')->term)) {
			return $c;
		}
		if ($this->search->prepareCallback and is_callable($this->search->prepareCallback)) {
			$term = ($this->search->prepareCallback)($term);
		}
		array_walk($this->search->searchFunction->args, fn(&$v) => $v = $v == '%term%' ? $term : $v);
		$c = $c->findBy(array_merge([$this->search->searchFunction->class], $this->search->searchFunction->args));
		if ($this->search->sortFunction) {
			array_walk($this->search->sortFunction->args, fn(&$v) => $v = $v == '%term%' ? $term : $v);
		}
		return $c;
	}


	private function sort(ICollection $c): ICollection
	{
		$sort = $this->getComponent('sorting')->sort;
		if ($sort) {
			$column = $this->columns[$sort];
			$direction = $this->getComponent('sorting')->direction;
			if ($column->sort->function) {
				$c = $c->orderBy([$column->sort->function->class], $direction);
			} else {
				$c = $c->orderBy($column->getNextrasName(), $direction);
			}
		}
		foreach ($this->columns as $column) {
			if (!$column->sort || !$column->sort->isDefault || $column->name == $sort) {
				continue;
			}
			if ($column->sort->function) {
				$c = $c->orderBy([$column->sort->function->class], $column->sort->direction);
			} else {
				$c = $c->orderBy($column->getNextrasName(), $column->sort->direction);
			}
		}

		if (
			$this->search
			&& $this->search->sortFunction
			&& $this->getComponent('searchForm')->term
			&& ($this->getComponent('searchForm-form')->isSubmitted() || !$sort)
		) {
			$c = $c->orderBy(array_merge([$this->search->sortFunction->class], (array) $this->search->sortFunction->args), $this->search->sortDirection);
		}
		$primaryKey = $this->repository->getEntityMetadata()->getPrimaryKey();
		return count($primaryKey) === 1 ? $c->orderBy($primaryKey[0]) : $c;
	}


	private function paginate(ICollection $c): ICollection
	{
		if ($this->itemsPerPage) {
			$c = $c->limitBy(
				$this->getComponent('pagination')->getPaginator()->length,
				$this->getComponent('pagination')->getPaginator()->offset
			);
		}
		return $c;
	}


	private function selectView(): View
	{
		if ($viewName = $this->getComponent('display')->viewName) {
			if (isset($this->views[$viewName])) {
				return $this->views[$viewName];
			}
		}

		foreach ($this->views as $view) {
			if ($view->isDefault) {
				return $view;
			}
		}
		return array_values($this->views)[0];
	}
}
