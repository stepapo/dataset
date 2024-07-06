<?php

declare(strict_types=1);

namespace Stepapo\Dataset;

use Stepapo\Utils\Schematic;
use Webovac\Core\Model\CmsDataRepository;


class LatteFilter extends Schematic
{
	public string $name;
	public ?array $args = null;
}
