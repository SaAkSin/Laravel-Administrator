# Field Type - Time

- [Usage](#usage)
- [Filter](#filter)

<a name="usage"></a>
## Usage


The `time` field type should be a TIME type in your database.

```php
'start_time' => array(
    'type' => 'time',
    'title' => 'Start Time',
    'time_format' => 'HH:mm',
)
```

The edit form uses the browser's native `input[type="time"]`. Keep `time_format` aligned with the value format expected by your model and database layer.

<a name="filter"></a>
## Filter


The `time` field filter comes with a start and end time. This allows you to narrow down the result set to a range, set only a minimum time, or set only a maximum time.
