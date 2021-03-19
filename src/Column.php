<?php

declare(strict_types=1);

namespace Stepapo\Data;

use Nextras\Orm\Collection\ICollection;


class Column
{
    public const ALIGN_LEFT = 'left';
    
    public const ALIGN_CENTER = 'center';
    
    public const ALIGN_RIGHT = 'right';

    public string $name;

    public ?string $label;

    public ?string $description;

    public ?int $width;

    public string $align;

    public string $columnName;

    public ?LatteFilter $latteFilter;

    public ?string $prepend;

    public ?string $append;

    public ?Link $link;

    public ?string $valueTemplateFile;

    public ?Sort $sort;

    public ?Filter $filter;

    public bool $hide;

    public ?string $class;


    public function __construct(
        string $name,
        ?string $label = null,
        ?string $description = null,
        ?int $width = null,
        string $align = self::ALIGN_LEFT,
        ?string $columnName = null,
        ?LatteFilter $latteFilter = null,
        ?string $prepend = null,
        ?string $append = null,
        ?Link $link = null,
        ?string $valueTemplateFile = null,
        ?Sort $sort = null,
        ?Filter $filter = null,
        bool $hide = false,
        ?string $class = null
    ) {
        $this->name = $name;
        $this->label = $label;
        $this->description = $description;
        $this->width = $width;
        $this->align = $align;
        $this->columnName = $columnName ?: $name;
        $this->latteFilter = $latteFilter;
        $this->prepend = $prepend;
        $this->append = $append;
        $this->link = $link;
        $this->valueTemplateFile = $valueTemplateFile;
        $this->sort = $sort;
        $this->filter = $filter;
        $this->hide = $hide;
        $this->class = $class;
    }
    
    
    public static function createFromArray(array $config, string $name): Column
    {
        $column = new self($name);
        if (array_key_exists('label', $config)) {
            $column->setLabel($config['label']);
        }
        if (array_key_exists('description', $config)) {
            $column->setDescription($config['description']);
        }
        if (array_key_exists('width', $config)) {
            $column->setWidth($config['width']);
        }
        if (array_key_exists('align', $config)) {
            $column->setAlign($config['align']);
        }
        if (array_key_exists('columnName', $config)) {
            $column->setColumnName($config['columnName']);
        }
        if (array_key_exists('latteFilter', $config)) {
            $column->setLatteFilter(LatteFilter::createFromArray((array) $config['latteFilter']));
        }
        if (array_key_exists('prepend', $config)) {
            $column->setPrepend($config['prepend']);
        }
        if (array_key_exists('append', $config)) {
            $column->setAppend($config['append']);
        }
        if (array_key_exists('link', $config)) {
            $column->setLink(Link::createFromArray((array) $config['link']));
        }
        if (array_key_exists('valueTemplateFile', $config)) {
            $column->setValueTemplateFile($config['valueTemplateFile']);
        }
        if (array_key_exists('sort', $config)) {
            $column->setSort(Sort::createFromArray((array) $config['sort']));
        }
        if (array_key_exists('filter', $config)) {
            $column->setFilter(Filter::createFromArray((array) $config['filter']));
        }
        if (array_key_exists('hide', $config)) {
            $column->setHide($config['hide']);
        }
        if (array_key_exists('class', $config)) {
            $column->setClass($config['class']);
        }
        return $column;
    }


    public function setName(string $name): Column
    {
        $this->name = $name;
        return $this;
    }


    public function setLabel(?string $label): Column
    {
        $this->label = $label;
        return $this;
    }


    public function setDescription(?string $description): Column
    {
        $this->description = $description;
        return $this;
    }


    public function setWidth(?int $width): Column
    {
        $this->width = $width;
        return $this;
    }


    public function setAlign(string $align): Column
    {
        $this->align = $align;
        return $this;
    }


    public function setColumnName(string $columnName): Column
    {
        $this->columnName = $columnName;
        return $this;
    }


    public function setLatteFilter(LatteFilter $latteFilter): Column
    {
        $this->latteFilter = $latteFilter;
        return $this;
    }


    /** @var string|array|null $args */
    public function createAndSetLatteFilter(string $name, $args): Column
    {
        $this->latteFilter = new LatteFilter($name, (array) $args);
        return $this;
    }


    public function setPrepend(?string $prepend): Column
    {
        $this->prepend = $prepend;
        return $this;
    }


    public function setAppend(?string $append): Column
    {
        $this->append = $append;
        return $this;
    }


    /** @var string|array|null $args */
    public function createAndSetLink(string $destination, $args): Column
    {
        $this->link = new Link($destination, (array) $args);
        return $this;
    }


    public function setLink(Link $link): Column
    {
        $this->link = $link;
        return $this;
    }


    public function setValueTemplateFile(?string $valueTemplateFile): Column
    {
        $this->valueTemplateFile = $valueTemplateFile;
        return $this;
    }


    public function setSort(Sort $sort): Column
    {
        $this->sort = $sort;
        return $this;
    }


    public function createAndSetSort(bool $isDefault = false, string $direction = ICollection::ASC): Column
    {
        $this->sort = new Sort($isDefault, $direction);
        return $this;
    }


    public function setFilter(Filter $filter): Column
    {
        $this->filter = $filter;
        return $this;
    }


    public function createAndSetFilter(
        ?array $options = null,
        ?string $prompt = null,
        ?string $columnName = null,
        ?string $function = null,
        ?int $collapse = null
    ): Filter
    {
        $this->filter = new Filter($options, $prompt, $columnName, $function, $collapse);
        return $this->filter;
    }


    public function setHide(bool $hide): Column
    {
        $this->hide = $hide;
        return $this;
    }


    public function setClass(?string $class): Column
    {
        $this->class = $class;
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


    public function getLatteName()
    {
        if (strpos($this->columnName, '.') !== false) {
            return str_replace('.', '_', $this->columnName);
        }

        return $this->columnName;
    }
}
