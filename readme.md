# Dataset

Component for Nette Framework, that helps generate datasets from Nextras ORM collections with paging, sorting and filtering capability.

## Example

Let's create a list of all persons in database.

### Definition

```neon
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
		sort:			
	birthday:
		label: Birthday
		align: right
		latteFilter:
			name: date
			args: j. n. Y
		sort:
views:
	table:
```

### Component

```php
public function createComponentPersonDataset()
{
	return Stepapo\Dataset\UI\Dataset::createFromNeon(__DIR__ . '/personDataset.neon', [
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

```neon
collection:
repository:
text: # include Text configuration
parentEntity:
itemsPerPage:
translator:
imageStorage:
search: # include Search configuration
itemClassCallback:
idColumnName:
alwaysRetrieveItems:
columns:
	example column: # include Column configuration
 	another example column: # include Column configuration
views:
	table: # include View configuration
	list: # include View configuration
```

### Column

```neon
label:
description:
width:
align:
columnName:
latteFilter: # include LatteFilter configuration
prepend:
append:
link: # include Link configuration
valueTemplateFile:
sort: # include Sort configuration
filter: # include Filter configuration
hide:
class:
```

### View

```neon
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

```neon
searchFunction: # include OrmFunction sonfiguration
placeholder:
prepareCallback:
suggestCallback:
sortFunction: # include OrmFunction sonfiguration 
```

### Text

```neon
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

```neon
options:
	example option: # include Option configuration
	another example option: # include Option configuration
prompt:
collapse:
columnName:
function:
hide:
```

### Sort

```neon
idDefault:
direction
function: # include OrmFunction configuration
```

### Link

```neon
destination:
args:
```

### LatteFilter

```neon
name:
args:
```

### Option

```neon
name:
label:
condition:
```

### OrmFunction

```neon
class:
args:
```
