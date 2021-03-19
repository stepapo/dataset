<?php

declare(strict_types=1);

namespace Stepapo\Data\UI\Dataset\Dataset;

use Stepapo\Data\Search;
use Stepapo\Data\UI\Dataset\DatasetControlTemplate;


class DatasetTemplate extends DatasetControlTemplate
{
	public ?Search $search;

	public int $count;
}
