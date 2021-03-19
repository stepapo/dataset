<?php

declare(strict_types=1);

namespace Stepapo\Data\UI;

use Nette\Localization\ITranslator;
use Stepapo\Data\Column;
use Stepapo\Data\Factory;
use Stepapo\Data\View;
use Nextras\Orm\Collection\ICollection;


interface MainComponent
{
    function getCollection(): ICollection;

	function getTranslator(): ?ITranslator;

    /** @return Column[]|null */
    function getColumns(): ?array;

    function getSelectedView(): View;

    function getFactory(): Factory;

    function getFilter(): array;
}
