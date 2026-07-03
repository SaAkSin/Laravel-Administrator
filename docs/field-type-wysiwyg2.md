# Field Type - WYSIWYG2

- [Usage](#usage)

<a name="usage"></a>
## Usage

The `wysiwyg2` field type should be a TEXT type in your database.

```php
'entry' => array(
	'type' => 'wysiwyg2',
	'title' => 'Entry',
)
```

In the edit form, an admin user will be presented with a modern Quill WYSIWYG editor. When the field is saved to the database, the resulting HTML is stored in the TEXT field.

> [!NOTE]
> The `wysiwyg2` type uses the lightweight **Quill** editor. If you prefer the classic, full-featured editor with advanced options like tables and raw source code editing, you can use the [WYSIWYG](/docs/field-type-wysiwyg) (`type => 'wysiwyg'`) field type which uses **CKEditor 4**.

Since the WYSIWYG is fairly large, you may want to think about [expanding your model's form width](/docs/model-configuration#form-width) to something like `400` or `500`.
