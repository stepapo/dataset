<?php

namespace Stepapo\Data\UI\Dataset\Item;

use Stepapo\Data\Column;
use Stepapo\Data\UI\Dataset\DatasetControlTemplate;
use Nextras\Orm\Entity\IEntity;


class ItemTemplate extends DatasetControlTemplate
{
    public IEntity $item;

    /** @var Column[] */
    public array $columns;
}
