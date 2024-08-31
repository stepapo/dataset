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
use Stepapo\Utils\Attribute\DefaultFromSchematic;
use Stepapo\Utils\Attribute\Type;
use Stepapo\Utils\Schematic;


class Dataset extends Schematic
{
	public ICollection $collection;
	public IRepository $repository;
	public ?int $itemsPerPage = null;
	public ?\Closure $itemClassCallback = null;
	public ?\Closure $itemLinkCallback = null;
	public ?string $itemListClass = null;
	public string $idColumnName = 'id';
	public bool $alwaysRetrieveItems = false;
	#[Type(Text::class), DefaultFromSchematic(Text::class)] public Text|array $text;
	#[Type(Search::class)] public Search|array|null $search = null;
	/** @var Column[] */ #[ArrayOfType(Column::class)] public array $columns = [];
	/** @var DatasetView[] */ #[ArrayOfType(DatasetView::class)] public array $views;
	public bool $hidePagination = false;
}
