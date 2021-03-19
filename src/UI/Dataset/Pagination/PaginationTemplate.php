<?php

declare(strict_types=1);

namespace Stepapo\Data\UI\Dataset\Pagination;

use Stepapo\Data\UI\Dataset\DatasetControlTemplate;
use Nette\Utils\Paginator;


class PaginationTemplate extends DatasetControlTemplate
{
	public Paginator $paginator;
}
