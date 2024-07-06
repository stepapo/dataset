<?php

declare(strict_types=1);

namespace Stepapo\Dataset;

use Contributte\ImageStorage\ImageStorage;
use Nette\Localization\Translator;
use Nextras\Orm\Entity\IEntity;
use Nextras\Orm\Repository\IRepository;
use Stepapo\Utils\Attribute\ArrayOfType;
use Stepapo\Utils\Attribute\DefaultValue;
use Stepapo\Utils\Attribute\DefaultValueFromSchematic;
use Stepapo\Utils\Attribute\Type;
use Nextras\Orm\Collection\ICollection;
use Stepapo\Utils\Schematic;


class Dataset extends Schematic
{
	public ICollection $collection;
	public IRepository $repository;
	public ?IEntity $parentEntity = null;
	public ?Translator $translator = null;
	public ?ImageStorage $imageStorage = null;
	public ?int $itemsPerPage = null;
	public $itemClassCallback = null;
	public ?string $itemListClass = null;
	public string $idColumnName = 'id';
	public bool $alwaysRetrieveItems = false;
	#[Type(Text::class), DefaultValueFromSchematic(Text::class)] public Text|array $text;
	#[Type(Search::class)] public Search|array|null $search = null;
	#[Type(Link::class)] public Link|array|null $itemLink = null;
	#[ArrayOfType(Column::class, 'name')] /** @var Column[] */ public array $columns = [];
	#[ArrayOfType(View::class, 'name')] /** @var View[] */ public array $views;
}
