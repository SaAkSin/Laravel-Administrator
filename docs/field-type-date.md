# Field Type - Date

- [Usage](#usage)
- [Filter](#filter)

<a name="usage"></a>
## Usage


The `date` field type should be a DATE or DATETIME type in your database.

```php
'date' => array(
    'type' => 'date',
    'title' => 'Date',
    'date_format' => 'yy-mm-dd',
)
```

The edit form uses the browser's native `input[type="date"]`. Keep `date_format` aligned with the value format expected by your model and database layer.

<a name="filter"></a>
## Filter


The `date` field filter comes with a start and end date. This allows you to narrow down the result set to a range, set only a minimum date, or set only a maximum date.
