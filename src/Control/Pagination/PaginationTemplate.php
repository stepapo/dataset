<?php

declare(strict_types=1);

namespace Stepapo\Dataset\Control\Pagination;

use Nette\Utils\Paginator;
use Stepapo\Dataset\Control\BaseTemplate;


class PaginationTemplate extends BaseTemplate
{
	public PaginationControl $control;

	public Paginator $paginator;

	public bool $shouldRenderNextPage;
}
