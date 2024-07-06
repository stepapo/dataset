<?php

declare(strict_types=1);

namespace Stepapo\Dataset;

use Stepapo\Utils\Schematic;


class OrmFunction extends Schematic
{
	public string $class;
	public ?array $args = null;
}
