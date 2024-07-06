<?php

declare(strict_types=1);

namespace Stepapo\Dataset;

use Stepapo\Utils\Schematic;


class Text extends Schematic
{
	public string $search = 'Hledat';
	public string $sort = 'Seřadit';
	public string $display = 'Zobrazit';
	public string $previous = 'Předchozí';
	public string $next = 'Další';
	public string $noResults = 'Nic nenalezeno.';
	public string $searchResults = 'Výsledky vyhledávání podle výrazu';
	public string $didYouMean = 'Mysleli jste';
}
