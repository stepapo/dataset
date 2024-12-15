<?php

declare(strict_types=1);

namespace Stepapo\Dataset;

use Nextras\Orm\Collection\ICollection;
use Nextras\Orm\Repository\IRepository;
use Stepapo\Data\Column;
use Stepapo\Data\Link;
use Stepapo\Data\Search;
use Stepapo\Data\Text;
use Stepapo\Utils\Attribute\ArrayOfType;
use Stepapo\Utils\Attribute\DefaultFromConfig;
use Stepapo\Utils\Attribute\Type;
use Stepapo\Utils\Config;


class Dataset extends Config
{
	public ICollection $collection;
	public IRepository $repository;
	public ?int $itemsPerPage = null;
	public ?\Closure $itemClassCallback = null;
	public ?\Closure $itemLinkCallback = null;
	public ?string $itemListClass = null;
	public string $idColumnName = 'id';
	public bool $isResponsive = true;
	public bool $alwaysRetrieveItems = false;
	#[Type(Text::class), DefaultFromConfig(Text::class)] public Text $text;
	#[Type(Search::class)] public ?Search $search = null;
	/** @var Column[] */ #[ArrayOfType(Column::class)] public array $columns = [];
	/** @var DatasetView[] */ #[ArrayOfType(DatasetView::class)] public array $views;
	public bool $hidePagination = false;
	public string $pagingMode = 'fromPreviousPage';
}
