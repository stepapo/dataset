<?php

declare(strict_types=1);

namespace Stepapo\Data\UI\Dataset\Pagination;

use Stepapo\Data\UI\Dataset\DatasetControl;
use Nette\Utils\Paginator;


/**
 * @property-read PaginationTemplate $template
 * @method onPaginate(SimplePagination $control)
 */
class SimplePagination extends DatasetControl implements Pagination
{
    public array $onPaginate = [];

    public int $page = 1;

    private Paginator $paginator;


    public function __construct(
        Paginator $paginator
    ) {
        $this->paginator = $paginator;
    }


    public function loadState(array $params): void
    {
        parent::loadState($params);
        $this->page = isset($params['page']) ? (int) $params['page'] : 1;
        $this->paginator->setPage($this->page);
    }



    public function render()
    {
        parent::render();
        $this->template->paginator = $this->paginator;
        $this->template->render($this->getSelectedView()->paginationTemplate);
    }


    public function handlePaginate(int $page = 1): void
    {
        $this->paginator->setPage($page);
        $this->page = $page;
        if ($this->presenter->isAjax()) {
            $this->onPaginate($this);
            $this->redrawControl();
        }
    }


    public function getPaginator(): Paginator
    {
        return $this->paginator;
    }
}
