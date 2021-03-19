<?php

declare(strict_types=1);

namespace Stepapo\Data\UI\Dataset\Filter;


/**
 * @method onFilter(Filter $control)
 */
interface Filter
{
    public function handleFilter($value = null): void;
}
