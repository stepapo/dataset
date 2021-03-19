<?php

declare(strict_types=1);

namespace Stepapo\Data;

use Nette\InvalidArgumentException;


class Search
{
    public OrmFunction $searchFunction;

    public ?string $placeholder;

    /** @var callable|null */
	public $prepareCallback;

	/** @var callable|null */
	public $suggestCallback;

	public ?OrmFunction $sortFunction;


	public function __construct(
        OrmFunction $searchFunction,
        ?string $placeholder = null,
		?callable $prepareCallback = null,
		?callable $suggestCallback = null,
		?OrmFunction $sortFunction = null
    ) {
		$this->searchFunction = $searchFunction;
		$this->placeholder = $placeholder;
		$this->prepareCallback = $prepareCallback;
		$this->suggestCallback = $suggestCallback;
		$this->sortFunction = $sortFunction;
	}


    public static function createFromArray(array $config): Search
    {
        if (!isset($config['searchFunction'])) {
            throw new InvalidArgumentException('Search function has to be defined.');
        }
        $searchFunction = OrmFunction::createFromArray((array) $config['searchFunction']);
        $search = new self($searchFunction);
		if (array_key_exists('placeholder', $config)) {
			$search->setPlaceholder($config['placeholder']);
		}
		if (array_key_exists('prepareCallback', $config)) {
			$search->setPrepareCallback($config['prepareCallback']);
		}
		if (array_key_exists('suggestCallback', $config)) {
			$search->setSuggestCallback($config['suggestCallback']);
		}
		if (array_key_exists('sortFunction', $config)) {
			$search->setSortFunction(OrmFunction::createFromArray((array) $config['sortFunction']));
		}
        return $search;
    }


	/** @var string|array|null $args */
	public function createAndSetSearchFunction(string $class, $args = null): Search
	{
		$this->searchFunction = new OrmFunction($class, (array) $args);
		return $this;
	}


	public function setSearchFunction(OrmFunction $searchFunction): Search
	{
		$this->searchFunction = $searchFunction;
		return $this;
	}


	public function setPlaceholder(?string $placeholder): Search
	{
		$this->placeholder = $placeholder;
		return $this;
	}


	public function setPrepareCallback(?callable $prepareCallback): Search
	{
		$this->prepareCallback = $prepareCallback;
		return $this;
	}


	public function setSuggestCallback(?callable $suggestCallback): Search
	{
		$this->suggestCallback = $suggestCallback;
		return $this;
	}


	/** @var string|array|null $args */
	public function createAndSetSortFunction(string $class, $args = null): Search
	{
		$this->sortFunction = new OrmFunction($class, (array) $args);
		return $this;
	}


	public function setSortFunction(?OrmFunction $sortFunction): Search
	{
		$this->sortFunction = $sortFunction;
		return $this;
	}
}
