<?php

declare(strict_types=1);

namespace Stepapo\Dataset;

use Stepapo\Utils\Attribute\CopyValue;
use Stepapo\Utils\Attribute\KeyProperty;
use Stepapo\Utils\Attribute\Type;
use Stepapo\Utils\Schematic;


class Column extends Schematic
{
	public const ALIGN_LEFT = 'left';
	public const ALIGN_CENTER = 'center';
	public const ALIGN_RIGHT = 'right';

	#[KeyProperty] public string $name;
	public ?string $label = null;
	public ?string $description = null;
	public ?int $width = null;
	public string $align = self::ALIGN_LEFT;
	#[CopyValue('name')] public ?string $columnName = null;
	public ?string $prepend = null;
	public ?string $append = null;
	public ?string $valueTemplateFile = null;
	public bool $hide = false;
	public ?string $class = null;
	#[Type(LatteFilter::class)] public LatteFilter|array|null $latteFilter = null;
	#[Type(Link::class)] public Link|array|null $link = null;
	#[Type(Sort::class)] public Sort|array|null $sort = null;
	#[Type(Filter::class)] public Filter|array|null $filter = null;


	public static function createFromArray(mixed $config = [], mixed $key = null, bool $skipDefaults = false): static
	{
		$column = parent::createFromArray($config, $key, $skipDefaults);
//		$column->columnName ??= $column->name;
		return $column;
	}


	public function getNextrasName()
	{
		if (str_contains($this->columnName, '.')) {
			return str_replace('.', '->', $this->columnName);
		}
		if (str_contains($this->columnName, '_')) {
			return str_replace('_', '->', $this->columnName);
		}
		return $this->columnName;
	}


	public function getLatteName()
	{
		if (str_contains($this->columnName, '.')) {
			return str_replace('.', '_', $this->columnName);
		}
		return $this->columnName;
	}
}
