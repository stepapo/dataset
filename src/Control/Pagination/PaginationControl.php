<?php

declare(strict_types=1);

namespace Stepapo\Dataset\Control\Pagination;

use Nette\Utils\Paginator;
use Stepapo\Data\Control\DataControl;
use Stepapo\Data\Text;
use Stepapo\Dataset\Control\Dataset\DatasetControl;


/**
 * @property-read PaginationTemplate $template
 * @method onPaginate(PaginationControl $control)
 */
class PaginationControl extends DataControl
{
	public array $onPaginate;
	public int $page = 1;


	public function __construct(
		private DatasetControl $main,
		private Paginator $paginator,
		private Text $text,
		private bool $hidePagination,
	) {}


	public function loadState(array $params): void
	{
		parent::loadState($params);
		$this->page = isset($params['page']) ? (int) $params['page'] : 1;
		$this->paginator->setPage($this->page);
	}



	public function render(): void
	{
		$this->template->paginator = $this->paginator;
		$this->template->text = $this->text;
		$this->template->shouldRenderNextPage = $this->shouldRenderNextPage();
		$this->template->hide = $this->hidePagination;
		$this->template->render($this->main->getView()->paginationTemplate);
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


	private function shouldRenderNextPage(): bool
	{
		return $this->main->getCurrentCount() > $this->paginator->getItemsPerPage();
	}
}
