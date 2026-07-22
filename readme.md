# Dataset

Component for Nette Framework, that helps generate datasets from Nextras ORM collections with paging, sorting and filtering capability. The docs show basic example and ways of configuring datasets.

## Example

Let's create a list of all persons in database sorted alphabeticaly by name. The list shows 10 items per page, is sortable by name, birthday and filterable by gender. Name contains a link to person's detail page. It is possible to switch between table view and grid view.

### Definition

```yml
collection: %collection%
repository: %repository%
itemsPerPage: 10
columns:
    fullname:
        label: Name
        link:
            destination: PersonDetail:deafult
            args:
                id: id
        sort:
            isDefault: true
    gender:
        label: Gender
        columnName: genderLabel
        filter:
            options: %genderFilterOptions%
            prompt: Both
    birthday:
        label: Birthday
        align: right
        latteFilter:
            name: date
            args: j. n. Y
        sort:
views:
    table:
    grid:
```

### Component

```php
public function createComponentPersonDataset()
{
    return Stepapo\Dataset\Control\Dataset::createFromNeon(__DIR__ . '/personDataset.yml', [
        'collection' => $this->orm->personRepository->findAll()
        'repository' => $this->orm->personRepository
        'genderFilterOptions' => ['m' => 'Male', 'f' => 'Female']
    ]);
}
```

### Template

```latte
{control personDataset}
```

## Configuration

### Dataset

```yml
collection:
repository:
parentEntity:
itemsPerPage:
translator:
imageStorage:
itemClassCallback:
itemLink: # include Link configuration
idColumnName:
alwaysRetrieveItems:
columns:
    example column: # include Column configuration
    another example column: # include Column configuration
views:
    table: # include View configuration
    list: # include View configuration
text: # include Text configuration
search: # include Search configuration
```

### Column

```yml
label:
description:
width:
align:
columnName:
prepend:
append:
valueTemplateFile:
hide:
class:
filter: # include Filter configuration
sort: # include Sort configuration
latteFilter: # include LatteFilter configuration
link: # include Link configuration
```

### View

```yml
label:
datasetTemplate:
itemListTemplate:
itemTemplate:
valueTemplate:
filterListTemplate:
filterTemplate:
paginationTemplate:
sortingTemplate:
displayTemplate:
searchTemplate:
itemFactoryCallback:
isDefault:
```

### Search

```yml
placeholder:
prepareCallback:
suggestCallback:
searchFunction: # include OrmFunction configuration
sortFunction: # include OrmFunction configuration
```

### Text

```yml
search:
sort:
display:
previous:
next:
noResults:
searchResults:
didYouMean:
```

### Filter

```yml
prompt:
collapse:
columnName:
function:
hide:
options:
    example option: # include Option configuration
    another example option: # include Option configuration
```

### Sort

```yml
idDefault:
direction:
function: # include OrmFunction configuration
```

### Link

```yml
destination:
args:
```

### LatteFilter

```yml
name:
args:
```

### Option

```yml
name:
label:
condition:
```

### OrmFunction

```yml
class:
args:
```
