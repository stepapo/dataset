<?php

declare(strict_types=1);

namespace Stepapo\Data\UI\Dataset\Dataset;

use Nette\Application\UI\Form;
use Nette\Localization\Translator;
use Nextras\Orm\Entity\IEntity;
use Nextras\Orm\Repository\IRepository;
use Stepapo\Data\Button;
use Stepapo\Data\Column;
use Stepapo\Data\Filter;
use Stepapo\Data\LatteFilter;
use Stepapo\Data\Link;
use Stepapo\Data\Option;
use Stepapo\Data\OrmFunction;
use Stepapo\Data\Sort;
use Stepapo\Data\Search;
use Stepapo\Data\UI\Dataset\DatasetControl;
use Stepapo\Data\UI\Dataset\DatasetView;
use Stepapo\Data\UI\Dataset\Display\DisplayControl;
use Stepapo\Data\UI\Dataset\FilterList\FilterListControl;
use Stepapo\Data\UI\Dataset\ItemList\ItemListControl;
use Stepapo\Data\UI\MainComponent;
use Stepapo\Data\UI\Dataset\Pagination\PaginationControl;
use Stepapo\Data\UI\Dataset\SearchForm\SearchFormControl;
use Stepapo\Data\UI\Dataset\Sorting\SortingControl;
use Stepapo\Data\Utils;
use Nette\InvalidArgumentException;
use Nette\Neon\Neon;
use Nette\Utils\FileSystem;
use Nette\Utils\Paginator;
use Nextras\Orm\Collection\ICollection;


/**
 * @property-read DatasetTemplate $template
 * @method onItemChange(Dataset $control, ?IEntity $entity)
 */
class Dataset extends DatasetControl implements MainComponent
{
	/** @var callable[] */
	public array $onItemChange;

	private DatasetView $selectedView;

	private ICollection $collection;

	private IRepository $repository;

	private ?IEntity $parentEntity;

	private ?Translator $translator;

	/** @var Column[]|null */
	private ?array $columns;

	/** @var DatasetView[]|null */
	private ?array $views;

	/** @var Button[]|null */
	private ?array $buttons;

	private ?int $itemsPerPage;

	private ?Search $search;

	/** @var callable|null */
	private $datasetCallback;

	/** @var callable|null */
	private $formCallback;

	private int $count = 0;

	public bool $shouldRetrieveItems = true;


	/**
	 * @param Column[]|null $columns
	 * @param DatasetView[]|null $views
	 */
	public function __construct(
		ICollection $collection,
		IRepository $repository,
		?IEntity $parentEntity = null,
		?Translator $translator = null,
		array $columns = [],
		array $views = [],
		array $buttons = [],
		?int $itemsPerPage = null,
		?Search $search = null,
		?callable $datasetCallback = null,
		?callable $formCallback = null,
	) {
		$this->collection = $collection;
		$this->repository = $repository;
		$this->parentEntity = $parentEntity;
		$this->columns = $columns;
		$this->views = $views;
		$this->buttons = $buttons;
		$this->itemsPerPage = $itemsPerPage;
		$this->search = $search;
		$this->translator = $translator;
		$this->datasetCallback = $datasetCallback;
		$this->formCallback = $formCallback;
	}


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
		$dataset = new self($config['collection'], $config['repository']);
		if (array_key_exists('parentEntity', $config)) {
			$dataset->setParentEntity($config['parentEntity']);
		}
		if (array_key_exists('itemsPerPage', $config)) {
			$dataset->setItemsPerPage($config['itemsPerPage']);
		}
		if (array_key_exists('translator', $config)) {
			$dataset->setTranslator($config['translator']);
		}
		if (array_key_exists('search', $config)) {
			$dataset->setSearch(Search::createFromArray((array) $config['search']));
		}
		if (array_key_exists('datasetCallback', $config)) {
			$dataset->setDatasetCallback($config['datasetCallback']);
		}
		if (array_key_exists('formCallback', $config)) {
			$dataset->setFormCallback($config['formCallback']);
		}
		if (array_key_exists('columns', $config)) {
			foreach ((array) $config['columns'] as $columnName => $columnConfig) {
				$dataset->addColumn(Column::createFromArray((array) $columnConfig, $columnName));
			}
		}
		if (array_key_exists('views', $config)) {
			foreach ((array) $config['views'] as $name => $viewConfig) {
				$dataset->addView(DatasetView::createFromArray((array) $viewConfig, $name));
			}
		}
		if (array_key_exists('buttons', $config)) {
			foreach ((array) $config['buttons'] as $name => $viewConfig) {
				$dataset->addButton(Button::createFromArray((array) $viewConfig, $name));
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
		$c = $this->getCollection();
		$c = $this->filter($c);
		$c = $this->search($c);
		return $c->countStored();
	}


	public function render()
	{
		parent::render();
		$this->template->showForm = (bool) $this->formCallback;
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


	public function getParentEntity(): ?IEntity
	{
		return $this->parentEntity;
	}


	public function getTranslator(): ?Translator
	{
		return $this->translator;
	}


	public function getDatasetCallback(): ?callable
	{
		return $this->datasetCallback;
	}


	public function getFormCallback(): ?callable
	{
		return $this->formCallback;
	}


	/** @return Column[]|null */
	public function getColumns(): ?array
	{
		return $this->columns;
	}


	/** @return DatasetView[]|null */
	public function getViews(): ?array
	{
		return $this->views;
	}


	/** @return Button[]|null */
	public function getButtons(): ?array
	{
		return $this->buttons;
	}


	public function getSelectedView(): DatasetView
	{
		return $this->selectedView;
	}


	public function getItemsPerPage(): ?int
	{
		return $this->itemsPerPage;
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


	public function setParentEntity(?IEntity $parentEntity): Dataset
	{
		$this->parentEntity = $parentEntity;
		return $this;
	}


	public function setTranslator(?ITranslator $translator): Dataset
	{
		$this->translator = $translator;
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


	/**
	 * @param string|array|null $latteFilterArgs
	 * @param string|array|null $linkArgs
	 */
	public function createAndAddColumn(
		string $name,
		?string $label = null,
		?string $description = null,
		?int $width = null,
		string $align = Column::ALIGN_LEFT,
		?string $columnName = null,
		?string $latteFilter = null,
		$latteFilterArgs = null,
		?string $prepend = null,
		?string $append = null,
		?string $link = null,
		$linkArgs = null,
		?string $valueTemplateFile = null,
		bool $sortable = false,
		bool $sortIsDefault = false,
		string $sortDefaultDirection = ICollection::ASC,
		?array $filterOptions = null,
		?string $filterPrompt = null,
		bool $hide = false,
		?string $class = null
	): Column
	{
		$this->columns[$name] = new Column(
			$name,
			$description,
			$label,
			$width,
			$align,
			$columnName,
			$latteFilter ? new LatteFilter($latteFilter, (array) $latteFilterArgs) : null,
			$prepend,
			$append,
			$link ? new Link($link, (array) $linkArgs) : null,
			$valueTemplateFile,
			$sortable ? new Sort($sortIsDefault, $sortDefaultDirection) : null,
			$filterOptions ? new Filter($filterOptions, $filterPrompt) : null,
			$hide,
			$class
		);
		return $this->columns[$name];
	}


	public function addView(DatasetView $view): Dataset
	{
		$this->views[$view->name] = $view;
		return $this;
	}


	public function createAndAddDefaultView(?string $name = null, bool $isDefault = false): DatasetView
	{
		if (!$name) {
			$defaultView = DatasetView::createDefault($isDefault);
			$this->views[$defaultView->name] = $defaultView;
			return $defaultView;
		}
		$view = DatasetView::createView($name, $isDefault);
		$this->views[$view->name] = $view;
		return $view;
	}


	public function createAndAddView(
		string $name,
		string $label,
		?string $datasetTemplate = DatasetView::VIEWS['list']['datasetTemplate'],
		?string $itemListTemplate = null,
		?string $itemTemplate = null,
		?string $attributeTemplate = null,
		?string $valueTemplate = DatasetView::VIEWS['list']['valueTemplate'],
		?string $filterListTemplate = DatasetView::VIEWS['list']['filterListTemplate'],
		?string $filterTemplate = DatasetView::VIEWS['list']['filterTemplate'],
		?string $paginationTemplate = DatasetView::VIEWS['list']['paginationTemplate'],
		?string $sortingTemplate = DatasetView::VIEWS['list']['sortingTemplate'],
		?string $displayTemplate = DatasetView::VIEWS['list']['displayTemplate'],
		?string $searchTemplate = DatasetView::VIEWS['list']['searchTemplate'],
		bool $isDefault = false
	): DatasetView
	{
		$this->views[$name] = new DatasetView(
			$name,
			$label,
			$datasetTemplate,
			$itemListTemplate,
			$itemTemplate,
			$attributeTemplate,
			$valueTemplate,
			$filterListTemplate,
			$filterTemplate,
			$paginationTemplate,
			$sortingTemplate,
			$displayTemplate,
			$searchTemplate,
			$isDefault,
		);
		return $this->views[$name];
	}


	public function addButton(Button $button): Dataset
	{
		$this->buttons[$button->name] = $button;
		return $this;
	}


	public function createAndAddButton(
		string $name,
		string $handleCallback,
		string $hideCallback,
		?string $label = null
	): DatasetView
	{
		$this->buttons[$name] = new Button(
			$name,
			$handleCallback,
			$hideCallback,
			$label
		);
		return $this->buttons[$name];
	}


	public function setSearch(Search $search): Dataset
	{
		$this->search = $search;
		return $this;
	}


	public function setDatasetCallback(callable $datasetCallback): Dataset
	{
		$this->datasetCallback = $datasetCallback;
		return $this;
	}


	public function setFormCallback(callable $formCallback): Dataset
	{
		$this->formCallback = $formCallback;
		return $this;
	}


	/**
	 * @var string|array|null $searchFunctionArgs
	 * @var string|array|null $sortFunctionArgs
	 */
	public function createAndSetSearch(
		string $searchFunctionClass,
		$searchFunctionArgs = null,
		?string $placeholder = null,
		?callable $prepareCallback = null,
		?callable $suggestCallback = null,
		?string $sortFunctionClass = null,
		$sortFunctionArgs = null
	): Dataset
	{
		$this->search = new Search(
			new OrmFunction($searchFunctionClass, (array) $searchFunctionArgs),
			$placeholder,
			$prepareCallback,
			$suggestCallback,
			new OrmFunction($sortFunctionClass, (array) $sortFunctionArgs)
		);
		return $this;
	}


	public function createComponentForm(): Form
	{
		return ($this->getFormCallback())($this, $this->getParentEntity());
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


	private function filter(ICollection $collection): ICollection
	{
		$c = $collection;
		foreach ($this->columns as $column) {
			if (!$column->filter) {
				continue;
			}
			$value = $this->getComponent('filtering')->getComponent('filter')->getComponent($column->name)->value;
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


	private function search(ICollection $collection): ICollection
	{
		$c = $collection;
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


	private function sort(ICollection $collection): ICollection
	{
		$c = $collection;
		$sort = $this->getComponent('sorting')->sort;
		if ($sort) {
			$column = $this->columns[$sort];
			$direction = $this->getComponent('sorting')->direction;
			if ($column->sort->function) {
				$c = $c->applyfunction($column->sort->function->class, $direction);
			} else {
				$c = $c->orderBy($column->getNextrasName(), $direction);
			}
		}
		foreach ($this->columns as $column) {
			if (!$column->sort || !$column->sort->isDefault || $column->name == $sort) {
				continue;
			}
			if ($column->sort->function) {
				$c = $c->applyfunction($column->sort->function->class, $column->sort->direction);
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
			$c = $c->applyFunction(...array_merge([$this->search->sortFunction->class], $this->search->sortFunction->args));
		}
		return $c;
	}


	private function paginate(ICollection $collection): ICollection
	{
		$c = $collection;
		if ($this->itemsPerPage) {
			$c = $c->limitBy(
				$this->getComponent('pagination')->getPaginator()->length,
				$this->getComponent('pagination')->getPaginator()->offset
			);
		}
		return $c;
	}


	private function selectView(): DatasetView
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
