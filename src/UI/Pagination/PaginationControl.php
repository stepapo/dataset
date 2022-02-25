<?php

declare(strict_types=1);

namespace Stepapo\Dataset\UI\Pagination;

use Nette\Application\Attributes\Persistent;
use Stepapo\Dataset\UI\DatasetControl;
use Nette\Utils\Paginator;


/**
 * @property-read PaginationTemplate $template
 * @method onPaginate(PaginationControl $control)
 */
class PaginationControl extends DatasetControl
{
	public array $onPaginate;

	#[Persistent]
	public int $page = 1;


	public function __construct(
		private Paginator $paginator
	) {}


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
		$this->template->shouldRenderNextPage = $this->shouldRenderNextPage();
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


	private function shouldRenderNextPage()
	{
		return $this->getCurrentCount() > $this->paginator->getItemsPerPage();
	}
}
