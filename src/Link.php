<?php

declare(strict_types=1);

namespace Stepapo\Dataset;

use Stepapo\Utils\Schematic;
use Webovac\Core\Model\CmsDataRepository;


class Link extends Schematic
{
	public string $destination;
	public ?array $args = null;
}
