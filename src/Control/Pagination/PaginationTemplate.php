<?php

declare(strict_types=1);

namespace Stepapo\Dataset\Control\Pagination;

use Nette\Utils\Paginator;
use Stepapo\Data\Control\DataTemplate;
use Stepapo\Data\Text;


class PaginationTemplate extends DataTemplate
{
	public PaginationControl $control;
	public Paginator $paginator;
	public bool $shouldRenderPreviousPage;
	public bool $shouldRenderNextPage;
	public Text $text;
	public bool $hide;
	public string $pagingMode;
}
