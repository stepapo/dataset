<?php

declare(strict_types=1);

namespace Stepapo\Dataset;

use Nette\Application\IPresenter;
use Nextras\Orm\Collection\ICollection;
use Nextras\Orm\Entity\IEntity;
use Nextras\Orm\Repository\IRepository;
use Stepapo\Data\Button;
use Stepapo\Data\Column;
use Stepapo\Data\Search;
use Stepapo\Data\Text;
use Stepapo\Dataset\Control\Dataset\DatasetControl;
use Stepapo\Utils\Attribute\ArrayOfType;
use Stepapo\Utils\Attribute\DefaultFromConfig;
use Stepapo\Utils\Attribute\Type;
use Stepapo\Utils\Config;


class Dataset extends Config
{
	public ICollection $collection;
	public IRepository $repository;
	/** @var \Closure(DatasetControl): array|null */ public ?\Closure $descriptionCallback = null;
	public ?int $labelWidth = null;
	public ?int $itemsPerPage = null;
	/** @var \Closure(IEntity): string|null */ public ?\Closure $itemClassCallback = null;
	/** @var \Closure(IEntity, IPresenter): string|null */ public ?\Closure $itemLinkCallback = null;
	public ?string $datasetClass = null;
	public ?string $itemListClass = null;
	public string $idColumnName = 'id';
	public bool $isResponsive = true;
	public bool $alwaysRetrieveItems = false;
	#[Type(Text::class), DefaultFromConfig(Text::class)] public Text $text;
	#[Type(Search::class)] public ?Search $search = null;
	/** @var Column[] */ #[ArrayOfType(Column::class)] public array $columns = [];
	/** @var Button[] */ #[ArrayOfType(Button::class)] public array $buttons = [];
	/** @var DatasetView[] */ #[ArrayOfType(DatasetView::class)] public array $views;
	public bool $hidePagination = false;
	public string $pagingMode = 'fromPreviousPage';


	protected static function getExtensionName(): string
	{
		return 'dataset';
	}
}
