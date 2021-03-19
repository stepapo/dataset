<?php

declare(strict_types=1);

namespace Stepapo\Data\UI\Dataset\ItemList;

use Stepapo\Data\UI\Dataset\DatasetControlTemplate;
use Nextras\Orm\Collection\ICollection;


class ItemListTemplate extends DatasetControlTemplate
{
    public ICollection $items;

    public array $crossItems;
}
