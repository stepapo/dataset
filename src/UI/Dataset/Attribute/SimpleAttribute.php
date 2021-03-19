<?php

declare(strict_types=1);

namespace Stepapo\Data\UI\Dataset\Attribute;

use Stepapo\Data\Column;
use Stepapo\Data\UI\Dataset\DatasetControl;
use Stepapo\Data\UI\Dataset\Value\Value;
use Nextras\Orm\Entity\IEntity;


/**
 * @property-read AttributeTemplate $template
 */
class SimpleAttribute extends DatasetControl implements Attribute
{
    private IEntity $entity;

    private Column $column;


    public function __construct(
        IEntity $entity,
        Column $column
    ) {
        $this->entity = $entity;
        $this->column = $column;
    }


    public function render()
    {
        parent::render();
        $this->template->column = $this->column;
        $this->template->render($this->getSelectedView()->attributeTemplate);
    }


    public function createComponentValue(): Value
    {
        return $this->getFactory()->createValue(
            $this->entity,
            $this->column
        );
    }
}
