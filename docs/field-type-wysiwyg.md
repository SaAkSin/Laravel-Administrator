# Field Type - WYSIWYG

- [Usage](#usage)

<a name="usage"></a>
## Usage

<img src="https://raw.github.com/FrozenNode/Laravel-Administrator/master/examples/images/field-type-wysiwyg.png" />

The `wysiwyg` field type should be a TEXT type in your database.

	'entry' => array(
		'type' => 'wysiwyg',
		'title' => 'Entry',
	)

In the edit form, an admin user will be presented with a CKEditor 4 WYSIWYG (Full Spec Toolbar). When the field is saved to the database, the resulting HTML is stored in the TEXT field.

> [!NOTE]
> The `wysiwyg` type uses the locally bundled **CKEditor 4** (Full Spec). If you prefer a lighter, more modern editor, you can use the [WYSIWYG2](/docs/field-type-wysiwyg2) (`type => 'wysiwyg2'`) field type which uses **Quill**.

Since the WYSIWYG is fairly large, you may want to think about [expanding your model's form width](/docs/model-configuration#form-width) to something like `400` or `500`.