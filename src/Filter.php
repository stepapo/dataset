<?php

declare(strict_types=1);

namespace Stepapo\Data;


class Filter
{
	public ?array $options = null;

	public ?string $prompt;

	public ?int $collapse;

	public ?string $function;

	public ?string $columnName;


	/** @var array|Option[]|null $options */
	public function __construct(
		?array $options = null,
		?string $prompt = null,
		?string $columnName = null,
		?string $function = null,
		?int $collapse = null
	) {
		$this->options = $options;
		$this->prompt = $prompt;
		$this->collapse = $collapse;
		$this->function = $function;
		$this->columnName = $columnName;
	}


	public static function createFromArray(array $config, array $params = []): Filter
	{
		$filter = new self();
		if (array_key_exists('options', $config)) {
			foreach ((array) $config['options'] as $name => $optionConfig) {
				$filter->addOption(Option::createFromArray((array) $optionConfig, $name));
			}
		}
		if (array_key_exists('prompt', $config)) {
			$filter->setPrompt($config['prompt']);
		}
		if (array_key_exists('collapse', $config)) {
			$filter->setCollapse($config['collapse']);
		}
		if (array_key_exists('columnName', $config)) {
			$filter->setColumnName($config['columnName']);
		}
		if (array_key_exists('function', $config)) {
			$filter->setFunction($config['function']);
		}
		return $filter;
	}


	public function getPrompt(): ?string
	{
		return $this->prompt;
	}


	public function setPrompt(?string $prompt): Filter
	{
		$this->prompt = $prompt;
		return $this;
	}


	public function getCollapse(): ?int
	{
		return $this->collapse;
	}


	public function setCollapse(?int $collapse): Filter
	{
		$this->collapse = $collapse;
		return $this;
	}


	public function getColumnName(): ?string
	{
		return $this->columnName;
	}


	public function setColumnName(?string $columnName): Filter
	{
		$this->columnName = $columnName;
		return $this;
	}


	public function getFunction(): ?string
	{
		return $this->function;
	}


	public function setFunction(?string $function): Filter
	{
		$this->function = $function;
		return $this;
	}


	public function addOption(Option $option): Filter
	{
		$this->options[$option->name] = $option;
		return $this;
	}


	public function createAndAddOption($name, $label, ?array $condition = null): Filter
	{
		$this->options[$name] = new Option(
			$name,
			$label,
			$condition
		);
		return $this;
	}


	public function getNextrasName(bool $withThis = true)
	{
		if (strpos($this->columnName, '.') !== false) {
			return ($withThis ? 'this->' : '') . str_replace('.', '->', $this->columnName);
		}

		if (strpos($this->columnName, '_') !== false) {
			return ($withThis ? 'this->' : '') . str_replace('_', '->', $this->columnName);
		}

		return $this->columnName;
	}

}
