<?php

declare(strict_types=1);

namespace Stepapo\Data\UI\Dataset\Value;


interface Value
{
    public function getEntityValue(?string $columnName = null);
}
