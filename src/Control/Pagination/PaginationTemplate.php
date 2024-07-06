<?php

declare(strict_types=1);

namespace Stepapo\Dataset\Control\Pagination;

use Stepapo\Dataset\Control\BaseTemplate;
use Nette\Utils\Paginator;


class PaginationTemplate extends BaseTemplate
{
	public PaginationControl $control;

	public Paginator $paginator;

	public bool $shouldRenderNextPage;
}
