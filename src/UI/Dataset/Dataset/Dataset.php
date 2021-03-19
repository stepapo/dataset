<?php

declare(strict_types=1);

namespace Stepapo\Data\UI\Dataset\Dataset;

use Nette\Localization\ITranslator;
use Stepapo\Data\Column;
use Stepapo\Data\Factory;
use Stepapo\Data\Filter;
use Stepapo\Data\LatteFilter;
use Stepapo\Data\Link;
use Stepapo\Data\Option;
use Stepapo\Data\OrmFunction;
use Stepapo\Data\Sort;
use Stepapo\Data\Search;
use Stepapo\Data\UI\Dataset\Attribute\SimpleAttribute;
use Stepapo\Data\UI\Dataset\DatasetControl;
use Stepapo\Data\UI\Dataset\DatasetFactory;
use Stepapo\Data\UI\Dataset\DatasetView;
use Stepapo\Data\UI\Dataset\Display\SimpleDisplay;
use Stepapo\Data\UI\Dataset\Display\Display;
use Stepapo\Data\UI\Dataset\Filter\SimpleFilter;
use Stepapo\Data\UI\Dataset\Filtering\SimpleFiltering;
use Stepapo\Data\UI\Dataset\Filtering\Filtering;
use Stepapo\Data\UI\Dataset\Item\SimpleItem;
use Stepapo\Data\UI\Dataset\ItemList\SimpleItemList;
use Stepapo\Data\UI\Dataset\ItemList\ItemList;
use Stepapo\Data\UI\MainComponent;
use Stepapo\Data\UI\Dataset\Pagination\SimplePagination;
use Stepapo\Data\UI\Dataset\Pagination\Pagination;
use Stepapo\Data\UI\Dataset\SearchForm\SearchForm;
use Stepapo\Data\UI\Dataset\Sorting\SimpleSorting;
use Stepapo\Data\UI\Dataset\Sorting\Sorting;
use Stepapo\Data\UI\Dataset\Value\SimpleValue;
use Stepapo\Data\Utils;
use Nette\InvalidArgumentException;
use Nette\Neon\Neon;
use Nette\Utils\FileSystem;
use Nette\Utils\Paginator;
use Nextras\Orm\Collection\ICollection;


/**
 * @property-read DatasetTemplate $template
 */
class Dataset extends DatasetControl implements MainComponent
{
    public const COMPONENT_LEVEL_LIST = 1;

    public const COMPONENT_LEVEL_ITEM = 2;

    public const COMPONENT_LEVEL_ATTRIBUTE = 3;

    private DatasetView $selectedView;

    private ICollection $collection;

	private ?ITranslator $translator;

    private ICollection $filteredCollection;

    private ICollection $searchedCollection;

    private ICollection $sortedCollection;

    private ICollection $paginatedCollection;

    /** @var Column[]|null */
    private ?array $columns;

    /** @var DatasetView[]|null */
    private ?array $views;

    private ?int $itemsPerPage;

    private ?int $componentLevel;

    private ?Search $search;

    private ?DatasetFactory $factory;

    private array $filter = [];

    private int $count = 0;


	/**
     * @param Column[]|null $columns
     * @param DatasetView[]|null $views
     */
    public function __construct(
        ICollection $collection,
        ?ITranslator $translator = null,
        ?array $columns = null,
        ?array $views = null,
        ?int $itemsPerPage = null,
        int $componentLevel = self::COMPONENT_LEVEL_LIST,
        ?Search $search = null,
        ?DatasetFactory $factory = null
    ) {
        $this->collection = $collection;
        $this->columns = $columns;
        $this->views = $views;
        $this->itemsPerPage = $itemsPerPage;
        $this->componentLevel = $componentLevel;
        $this->search = $search;
        $this->factory = $factory ?: DatasetFactory::createDefault();
		$this->translator = $translator;
	}
    
    
    public static function createFromNeon(string $file, array $params = []): Dataset
    {
        $config = (array) Neon::decode(FileSystem::read($file));
        $parsedConfig = Utils::replaceParams($config, $params);
        return self::createFromArray((array) $parsedConfig);
    }


    public static function createFromArray(array $config): Dataset
    {
        if (!isset($config['collection'])) {
            throw new InvalidArgumentException('Dataset collection has to be defined.');
        }
        $dataset = new self($config['collection']);
        if (array_key_exists('itemsPerPage', $config)) {
            $dataset->setItemsPerPage($config['itemsPerPage']);
        }
		if (array_key_exists('translator', $config)) {
			$dataset->setTranslator($config['translator']);
		}
        if (array_key_exists('componentLevel', $config)) {
            $dataset->setComponentLevel($config['componentLevel']);
        }
        if (array_key_exists('factory', $config)) {
            $dataset->setFactory(DatasetFactory::createFromArray((array) $config['factory']));
        }
        if (array_key_exists('search', $config)) {
            $dataset->setSearch(Search::createFromArray((array) $config['search']));
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
        return $dataset;
    }


    public function loadState(array $params): void
    {
        parent::loadState($params);
        if (!$this->views) {
            $this->createAndAddDefaultView();
        }
        $this->selectedView = $this->selectView();
		$this->filter()->search()->sort();
		$this->count = $this->searchedCollection->countStored();
		$this->paginate();
    }


    public function render()
    {
        parent::render();
        $this->template->search = $this->search;
        $this->template->count = $this->count;
		$term = $this->getComponent('searchForm')->term;
		$this->template->term = $term;
        if ($this->count == 0 && $term && $this->search->suggestCallback) {
        	$this->template->suggestedTerm = ($this->search->suggestCallback)($term);
		}
        $this->template->render($this->selectedView->datasetTemplate);
    }



    public function getCollection(): ICollection
    {
        return $this->collection;
    }


    public function getTranslator(): ?ITranslator
	{
		return $this->translator;
	}


    public function getComponentLevel(): int
    {
        return $this->componentLevel;
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


    public function getSelectedView(): DatasetView
    {
        return $this->selectedView;
    }


    public function getFactory(): Factory
    {
        return $this->factory;
    }


    public function getFilter(): array
    {
        return $this->filter;
    }


    public function setCollection(ICollection $collection): Dataset
    {
        $this->collection = $collection;
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


    public function setComponentLevel(?int $componentLevel): Dataset
    {
        $this->componentLevel = $componentLevel;
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
        ?string $filteringTemplate = DatasetView::VIEWS['list']['filteringTemplate'],
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
            $filteringTemplate,
            $filterTemplate,
            $paginationTemplate,
            $sortingTemplate,
            $displayTemplate,
            $searchTemplate,
            $isDefault,
        );
        return $this->views[$name];
    }

    public function setSearch(Search $search): Dataset
    {
        $this->search = $search;
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
	): Dataset {
        $this->search = new Search(
        	new OrmFunction($searchFunctionClass, (array) $searchFunctionArgs),
			$placeholder,
			$prepareCallback,
			$suggestCallback,
			new OrmFunction($sortFunctionClass, (array) $sortFunctionArgs)
		);
        return $this;
    }


    public function setFactory(DatasetFactory $factory): Dataset
    {
        $this->factory = $factory;
        return $this;
    }


    public function createAndSetFactory(
        ?string $itemListClass = SimpleItemList::class,
        ?string $itemClass = SimpleItem::class,
        ?string $attributeClass = SimpleAttribute::class,
        ?string $valueClass = SimpleValue::class,
        ?string $filteringClass = SimpleFiltering::class,
        ?string $filterClass = SimpleFilter::class,
        ?string $paginationClass = SimplePagination::class,
        ?string $sortingClass = SimpleSorting::class,
        ?string $displayClass = SimpleDisplay::class
    ): DatasetFactory {
        $this->factory = new DatasetFactory(
            $itemListClass,
            $itemClass,
            $attributeClass,
            $valueClass,
            $filteringClass,
            $filterClass,
            $paginationClass,
            $sortingClass,
            $displayClass
        );
        return $this->factory;
    }


    public function createComponentList(): ItemList
    {
        return $this->getFactory()->createItemList($this->paginatedCollection);
    }


    public function createComponentPagination(): Pagination
    {
        $pagination = $this->getFactory()->createPagination(
            (new Paginator)->setItemsPerPage($this->itemsPerPage),
        );
        $pagination->onPaginate[] = function (Pagination $pagination) {
            $this->getComponent('list')->redrawControl();
        };
        return $pagination;
    }


    public function createComponentFiltering(): Filtering
    {
        $control = $this->getFactory()->createFiltering();
        $control->onFilter[] = function (Filtering $filtering) {
            $this->redrawControl();
        };
        return $control;
    }


    public function createComponentSearchForm(): SearchForm
    {
        $control = $this->getFactory()->createSearchForm($this->search->placeholder);
        $control->onSearch[] = function (SearchForm $search) {
            $this->redrawControl();
        };
        return $control;
    }


    public function createComponentSorting(): Sorting
    {
        $control = $this->getFactory()->createSorting();
        $control->onSort[] = function (Sorting $sorting) {
            $this->redrawControl();
        };
        return $control;
    }


    public function createComponentDisplay(): Display
    {
        $control = $this->getFactory()->createDisplay();
        $control->onDisplay[] = function (Display $display) {
            $this->redrawControl();
        };
        return $control;
    }


    private function filter(): Dataset
    {
        $this->filteredCollection = $this->collection;
        foreach ($this->columns as $column) {
            if (!$column->filter) {
                continue;
            }
            $value = $this->getComponent('filtering')->getComponent('filter')->getComponent($column->name)->value;
            if (!$value) {
                continue;
            }
            $this->filter[$column->name] = $value;
            if ($column->filter->options[$value] instanceof Option && $column->filter->options[$value]->condition) {
                $this->filteredCollection = $this->filteredCollection->findBy($column->filter->options[$value]->condition);
            } elseif ($column->filter->function) {
                $this->filteredCollection = $this->filteredCollection->findBy([$column->filter->function, $column->getNextrasName(), $value]);
            } else {
                $this->filteredCollection = $this->filteredCollection->findBy([$column->filter->columnName ? $column->filter->getNextrasName() : $column->name => $value]);
            }
        }
        return $this;
    }


    private function search(): Dataset
    {
        $this->searchedCollection = $this->filteredCollection;
        if (!$this->search || !($term = $this->getComponent('searchForm')->term)) {
            return $this;
        }
        if ($this->search->prepareCallback and is_callable($this->search->prepareCallback)) {
        	$term = ($this->search->prepareCallback)($term);
		}
        array_walk($this->search->searchFunction->args, fn(&$v) => $v = $v == '%term%' ? $term : $v);
        $this->searchedCollection = $this->searchedCollection->findBy(array_merge([$this->search->searchFunction->class], $this->search->searchFunction->args));
        if ($this->search->sortFunction) {
			array_walk($this->search->sortFunction->args, fn(&$v) => $v = $v == '%term%' ? $term : $v);
		}
        return $this;
    }


    private function sort(): Dataset
    {
        $this->sortedCollection = $this->searchedCollection;
		$sort = $this->getComponent('sorting')->sort;
		if ($sort) {
			$column = $this->columns[$sort];
			$direction = $this->getComponent('sorting')->direction;
			if ($column->sort->function) {
				$this->sortedCollection = $this->sortedCollection->applyfunction($column->sort->function->class, $direction);
			} else {
				$this->sortedCollection = $this->sortedCollection->orderBy($column->getNextrasName(), $direction);
			}
		}
		foreach ($this->columns as $column) {
			if (!$column->sort || !$column->sort->isDefault || $column->name == $sort) {
				continue;
			}
			if ($column->sort->function) {
				$this->sortedCollection = $this->sortedCollection->applyfunction($column->sort->function->class, $column->sort->direction);
			} else {
				$this->sortedCollection = $this->sortedCollection->orderBy($column->getNextrasName(), $column->sort->direction);
			}

		}

		if (
			$this->search
			&& $this->search->sortFunction
			&& $this->getComponent('searchForm')->term
			&& ($this->getComponent('searchForm-form')->isSubmitted() || !$sort)
		) {
			$this->sortedCollection = $this->sortedCollection->applyFunction(...array_merge([$this->search->sortFunction->class], $this->search->sortFunction->args));
		}

        return $this;
    }


    private function paginate(): Dataset
    {
        $this->paginatedCollection = $this->sortedCollection;
        if ($this->itemsPerPage) {
            $this->getComponent('pagination')->getPaginator()->setItemCount($this->count);
            $this->paginatedCollection = $this->paginatedCollection->limitBy(
                $this->getComponent('pagination')->getPaginator()->length,
                $this->getComponent('pagination')->getPaginator()->offset
            );
        }
        return $this;
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
