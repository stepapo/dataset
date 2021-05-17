<?php

declare(strict_types=1);

namespace Stepapo\Dataset\UI\Pagination;

use Stepapo\Dataset\UI\DatasetControlTemplate;
use Nette\Utils\Paginator;


class PaginationTemplate extends DatasetControlTemplate
{
	public PaginationControl $control;

	public Paginator $paginator;
}
