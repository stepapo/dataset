<?php

declare(strict_types=1);

namespace Stepapo\Dataset;

use Stepapo\Utils\Schematic;


class Option extends Schematic
{
	public int|string|null $name;
	public int|string|null $label = null;
	public ?array $condition = null;
}
