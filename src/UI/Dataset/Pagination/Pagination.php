<?php

declare(strict_types=1);

namespace Stepapo\Data\UI\Dataset\Pagination;

use Nette\Utils\Paginator;


/**
 * @method onPaginate(Pagination $control)
 */
interface Pagination
{
	public function getPaginator(): Paginator;

	public function handlePaginate(int $page = 1): void;
}
