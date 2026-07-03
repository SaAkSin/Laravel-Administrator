# Field Type - Datetime

- [Usage](#usage)
- [Filter](#filter)

<a name="usage"></a>
## Usage


The `datetime` field type should be a DATETIME type in your database.

```php
'published_at' => array(
    'type' => 'datetime',
    'title' => 'Published At',
    'date_format' => 'yy-mm-dd',
    'time_format' => 'HH:mm',
)
```

The edit form uses the browser's native `input[type="datetime-local"]`. Keep `date_format` and `time_format` aligned with the value format expected by your model and database layer.

<a name="filter"></a>
## Filter


The `datetime` field filter comes with a start and end datetime. This allows you to narrow down the result set to a range, set only a minimum datetime, or set only a maximum datetime.
