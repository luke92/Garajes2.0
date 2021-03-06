# Tables

> For displaying tabular data. `<b-table>` supports pagination, filtering, sorting, custom
> rendering, events, and asynchronous data.

**Example: Basic usage**

```html
<template>
  <b-table striped hover :items="items" />
</template>

<script>
  const items = [
    { isActive: true, age: 40, first_name: 'Dickerson', last_name: 'Macdonald' },
    { isActive: false, age: 21, first_name: 'Larsen', last_name: 'Shaw' },
    { isActive: false, age: 89, first_name: 'Geneva', last_name: 'Wilson' },
    { isActive: true, age: 38, first_name: 'Jami', last_name: 'Carney' }
  ]

  export default {
    data() {
      return {
        items: items
      }
    }
  }
</script>

<!-- table-basic-1.vue -->
```

## Items (record data)

`items` is the table data in array format, where each record (row) data are keyed objects. Example
format:

```js
const items = [
  { age: 32, first_name: 'Cyndi' },
  { age: 27, first_name: 'Havij' },
  { age: 42, first_name: 'Robert' }
]
```

`<b-table>` automatically samples the first row to extract field names (the keys in the record
data). Field names are automatically "humanized" by converting `kebab-case`, `snake_case`, and
`camelCase` to individual words and capitalizes each word. Example conversions:

- `first_name` becomes `First Name`
- `last-name` becomes `Last Name`
- `age` becoms `Age`
- `YEAR` remains `YEAR`
- `isActive` becomes `Is Active`

These titles will be displayed in the table header, in the order they appear in the **first** record
of data. See the [**Fields**](#fields-column-definitions-) section below for customizing how field
headings appear.

**Note:** Field order is not guaranteed. Fields will typically appear in the order they were defined
in the first row, but this may not always be the case depending on the version of browser in use.
See section [**Fields (column definitions)**](#fields-column-definitions-) below to see how to
guarantee the order of fields.

Record data may also have additional special reserved name keys for colorizing rows and individual
cells (variants), and for triggering additional row detail. The supported optional item record
modifier properties (make sure your field keys do not conflict with these names):

| Property        | Type    | Description                                                                                                                                                                                                                                     |
| --------------- | ------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `_cellVariants` | Object  | Bootstrap contextual state applied to individual cells. Keyed by field (Supported values: `active`, `success`, `info`, `warning`, `danger`). These variants map to classes `table-${variant}` or `bg-${variant}` (when the `dark` prop is set). |
| `_rowVariant`   | String  | Bootstrap contextual state applied to the entire row (Supported values: `active`, `success`, `info`, `warning`, `danger`). These variants map to classes `table-${variant}` or `bg-${variant}` (when the `dark` prop is set)                    |
| `_showDetails`  | Boolean | Used to trigger the display of the `row-details` scoped slot. See section [Row details support](#row-details-support) below for additional information                                                                                          |

**Example: Using variants for table cells**

```html
<template>
  <b-table hover :items="items" />
</template>

<script>
  const items = [
    { isActive: true, age: 40, first_name: 'Dickerson', last_name: 'Macdonald' },
    { isActive: false, age: 21, first_name: 'Larsen', last_name: 'Shaw' },
    {
      isActive: false,
      age: 89,
      first_name: 'Geneva',
      last_name: 'Wilson',
      _rowVariant: 'danger'
    },
    {
      isActive: true,
      age: 40,
      first_name: 'Thor',
      last_name: 'Macdonald',
      _cellVariants: { isActive: 'success', age: 'info', first_name: 'warning' }
    },
    { isActive: false, age: 29, first_name: 'Dick', last_name: 'Dunlap' }
  ]

  export default {
    data() {
      return {
        items: items
      }
    }
  }
</script>

<!-- table-variants-1.vue -->
```

`items` can also be a reference to a _provider_ function, which returns an `Array` of items data.
Provider functions can also be asynchronous:

- By returning `null` (or `undefined`) and calling a callback, when the data is ready, with the data
  array as the only argument to the callback,
- By returning a `Promise` that resolves to an array.

See the [**"Using Items Provider functions"**](#using-items-provider-functions) section below for
more details.

## Fields (column definitions)

The `fields` prop is used to customize the table columns headings, and in which order the columns of
data are displayed. The field object keys (i.e. `age` or `first_name` as shown below) are used to
extract the value from each item (record) row, and to provide additional fetures such as enabling
[**sorting**](#sorting) on the column, etc.

Fields can be provided as a _simple array_, an _array of objects_, or an _object_. **Internally the
fields data will be normalized into the _array of objects_ format**. Events or slots that include
the column `field` data will be in the normalized field object format (array of objects for
`fields`, or an object for an individual `field`).

### Fields as a simple array

Fields can be a simple array, for defining the order of the columns, and which columns to display.
**(field order is guaranteed)**:

**Example: Using `array` fields definition**

```html
<template>
  <b-table striped hover :items="items" :fields="fields" />
</template>

<script>
  export default {
    data() {
      return {
        // Note 'isActive' is left out and will not appear in the rendered table
        fields: ['first_name', 'last_name', 'age'],
        items: [
          { isActive: true, age: 40, first_name: 'Dickerson', last_name: 'Macdonald' },
          { isActive: false, age: 21, first_name: 'Larsen', last_name: 'Shaw' },
          { isActive: false, age: 89, first_name: 'Geneva', last_name: 'Wilson' },
          { isActive: true, age: 38, first_name: 'Jami', last_name: 'Carney' }
        ]
      }
    }
  }
</script>

<!-- table-fields-array.vue -->
```

### Fields as an array of objects

Fields can be a an array of objects, providing additional control over the fields (such as sorting,
formatting, etc). Only columns (keys) that appear in the fields array will be shown **(field order
is guaranteed)**:

**Example: Using array of objects fields definition**

```html
<template>
  <b-table striped hover :items="items" :fields="fields" />
</template>

<script>
  export default {
    data() {
      return {
        // Note 'isActive' is left out and will not appear in the rendered table
        fields: [
          {
            key: 'last_name',
            sortable: true
          },
          {
            key: 'first_name',
            sortable: false
          },
          {
            key: 'age',
            label: 'Person age',
            sortable: true,
            // Variant applies to the whole column, including the header and footer
            variant: 'danger'
          }
        ],
        items: [
          { isActive: true, age: 40, first_name: 'Dickerson', last_name: 'Macdonald' },
          { isActive: false, age: 21, first_name: 'Larsen', last_name: 'Shaw' },
          { isActive: false, age: 89, first_name: 'Geneva', last_name: 'Wilson' },
          { isActive: true, age: 38, first_name: 'Jami', last_name: 'Carney' }
        ]
      }
    }
  }
</script>

<!-- table-fields-array-of-objects.vue -->
```

### Fields as an object

Also, fields can be a an object providing similar control over the fields as the _array of objects_
above does. Only columns listed in the fields object will be shown. The order of the fields will
typically be in the order they were defined in the object, although **field order is not guaranteed
(this may cause issues with Server Side Rendering and client rehydration)**.

**Example: Using object fields definition**

```html
<template>
  <b-table striped hover :items="items" :fields="fields" />
</template>

<script>
  export default {
    data() {
      return {
        // Note 'isActive' is left out and will not appear in the rendered table
        fields: {
          last_name: {
            label: 'Person last name',
            sortable: true
          },
          first_name: {
            label: 'Person first name',
            sortable: false
          },
          foo: {
            // This key overrides `foo`!
            key: 'age',
            label: 'Person age',
            sortable: true
          },
          city: {
            key: 'address.city',
            sortable: true
          },
          'address.country': {
            label: 'Country',
            sortable: true
          }
        },
        items: [
          {
            isActive: true,
            age: 40,
            first_name: 'Dickerson',
            last_name: 'Macdonald',
            address: { country: 'USA', city: 'New York' }
          },
          {
            isActive: false,
            age: 21,
            first_name: 'Larsen',
            last_name: 'Shaw',
            address: { country: 'Canada', city: 'Toronto' }
          },
          {
            isActive: false,
            age: 89,
            first_name: 'Geneva',
            last_name: 'Wilson',
            address: { country: 'Australia', city: 'Sydney' }
          },
          {
            isActive: true,
            age: 38,
            first_name: 'Jami',
            last_name: 'Carney',
            address: { country: 'England', city: 'London' }
          }
        ]
      }
    }
  }
</script>

<!-- table-fields-object.vue -->
```

**Notes:**

- if a `key` property is defined in the field definition, it will take precedence over the key used
  to define the field.

### Field definition reference

The following field properties are recognized:

| Property        | Type                        | Description                                                                                                                                                                                                                                                                                                           |
| --------------- | --------------------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `key`           | String                      | The key for selecting data from the record in the items array. Required when setting the `fields` from as an array of objects.                                                                                                                                                                                        |
| `label`         | String                      | Appears in the columns table header (and footer if `foot-clone` is set). Defaults to the field's key (in humanized format) if not provided. It's possible to use empty labels by assigning an empty string `""` but be sure you also set `headerTitle` to provide non-sighted users a hint about the column contents. |
| `headerTitle`   | String                      | Text to place on the fields header `<th>` attribute `title`. Defaults to no `title` attribute.                                                                                                                                                                                                                        |
| `headerAbbr`    | String                      | Text to place on the fields header `<th>` attribute `abbr`. Set this to the unabbreviated version of the label (or title) if label (or title) is an abbreviation. Defaults to no `abbr` attribute.                                                                                                                    |
| `class`         | String or Array             | Class name (or array of class names) to add to `<th>` **and** `<td>` in the column.                                                                                                                                                                                                                                   |
| `formatter`     | String or Function          | A formatter callback function, can be used instead of (or in conjunction with) slots for real table fields (i.e. fields, that have corresponding data at items array). Refer to [**Custom Data Rendering**](#custom-data-rendering) for more details.                                                                 |
| `sortable`      | Boolean                     | Enable sorting on this column. Refer to the [**Sorting**](#sorting) Section for more details.                                                                                                                                                                                                                         |
| `sortDirection` | String                      | Change sort direction on this column. Refer to the [**Change sort direction**](#change-sort-direction) Section for more details.                                                                                                                                                                                      |
| `tdClass`       | String or Array or Function | Class name (or array of class names) to add to `<tbody>` data `<td>` cells in the column. If custom classes per cell are required, a callback function can be specified instead.                                                                                                                                      |
| `thClass`       | String or Array             | Class name (or array of class names) to add to `<thead>`/`<tfoot>` heading `<th>` cell.                                                                                                                                                                                                                               |
| `thStyle`       | Object                      | JavaScript object representing CSS styles you would like to apply to the table `<thead>`/`<tfoot>` field `<th>`.                                                                                                                                                                                                      |
| `variant`       | String                      | Apply contextual class to all the `<th>` **and** `<td>` in the column - `active`, `success`, `info`, `warning`, `danger`. These variants map to classes `thead-${variant}` (in the header), `table-${variant}` (in the body), or `bg-${variant}` (when table prop `dark` is set).                                     |
| `tdAttr`        | Object or Function          | JavaScript object representing additional attributes to apply to the `<tbody>` field `<td>` cell. If custom attributes per cell are required, a callback function can be specified instead.                                                                                                                           |
| `isRowHeader`   | Boolean                     | When set to `true`, the field's item data cell will be rendered with `<th>` rather than the default of `<td>`.                                                                                                                                                                                                        |

**Notes:**

- _Field properties, if not present, default to `null` (falsey) unless otherwise stated above._
- _`class`, `thClass`, `tdClass` etc. will not work with classes that are defined in scoped CSS_
- _For information on the syntax supported by `thStyle`, see
  [Class and Style Bindings](https://vuejs.org/v2/guide/class-and-style.html#Binding-Inline-Styles)
  in the Vue.js guide._
- _Any additional properties added to the field objects will be left intact - so you can access them
  via the named scoped slots for custom data, header, and footer rendering._

For information and usage about scoped slots and formatters, refer to the
[**Custom Data Rendering**](#custom-data-rendering) section below.

Feel free to mix and match simple array and object array together:

```js
fields: [{ key: 'first_name', label: 'First' }, { key: 'last_name', label: 'Last' }, 'age', 'sex']
```

### Primary key

`<b-table>` provides an additional prop `primary-key`, which you can use to identify the field key
that uniquely identifies the row.

This value is used by `<b-table>` to help Vue optimize the rendering of table rows. Internally, the
the value of the field key specified by the `primary-key` prop is used as the Vue `:key` value for
each rendered item row `<tr>` element. The value specified by the column key **must be** either a
`string` or `number`, and **must be** unique accross all rows in the table.

If you are seeing rendering issue (i.e. tooltips hiding when item data changes or is
sorted/filtered), setting the `primary-key` prop (if you have a unique identifier per row) can
alleviate these issues.

Specifying the `primary-key` column is handy if you are using 3rd party table transitions or drag
and drop plugins, as they rely on having a consitent and unique per row `:key` value.

In future releases of BootstrapVue, the `primary-key` may be used for additional features.

## Table style options

### Table Styling

`<b-table>` provides several props to alter the style of the table:

| prop           | Type              | Description                                                                                                                                                                                                                                                                                                                                        |
| -------------- | ----------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `striped`      | Boolean           | Add zebra-striping to the table rows within the `<tbody>`                                                                                                                                                                                                                                                                                          |
| `bordered`     | Boolean           | For borders on all sides of the table and cells.                                                                                                                                                                                                                                                                                                   |
| `borderless`   | Boolean           | removes inner borders from table.                                                                                                                                                                                                                                                                                                                  |
| `outlined`     | Boolean           | For a thin border on all sides of the table. Has no effect if `bordered` is set.                                                                                                                                                                                                                                                                   |
| `small`        | Boolean           | To make tables more compact by cutting cell padding in half.                                                                                                                                                                                                                                                                                       |
| `hover`        | Boolean           | To enable a hover highlighting state on table rows within a `<tbody>`                                                                                                                                                                                                                                                                              |
| `dark`         | Boolean           | Invert the colors ??? with light text on dark backgrounds (equivalent to Bootstrap V4 class `.table-dark`)                                                                                                                                                                                                                                           |
| `fixed`        | Boolean           | Generate a table with equal fixed-width columns (`table-layout: fixed`)                                                                                                                                                                                                                                                                            |
| `foot-clone`   | Boolean           | Turns on the table footer, and defaults with the same contents a the table header                                                                                                                                                                                                                                                                  |
| `responsive`   | Boolean or String | Generate a responsive table to make it scroll horizontally. Set to `true` for an always responsive table, or set it to one of the breakpoints `'sm'`, `'md'`, `'lg'`, or `'xl'` to make the table responsive (horizontally scroll) only on screens smaller than the breakpoint. See [**Responsive tables**](#responsive-tables) below for details. |
| `stacked`      | Boolean or String | Generate a responsive stacked table. Set to `true` for an always stacked table, or set it to one of the breakpoints `'sm'`, `'md'`, `'lg'`, or `'xl'` to make the table visually stacked only on screens smaller than the breakpoint. See [**Stacked tables**](#stacked-tables) below for details.                                                 |
| `head-variant` | String            | Use `'light'` or `'dark'` to make table header appear light or dark gray, respectively                                                                                                                                                                                                                                                             |
| `foot-variant` | String            | Use `'light'` or `'dark'` to make table footer appear light or dark gray, respectively. If not set, `head-variant` will be used. Has no effect if `foot-clone` is not set                                                                                                                                                                          |

**Example: Basic table styles**

```html
<template>
  <div>
    <b-form-group label="Table Options">
      <b-form-checkbox inline v-model="striped">Striped</b-form-checkbox>
      <b-form-checkbox inline v-model="bordered">Bordered</b-form-checkbox>
      <b-form-checkbox inline v-model="borderless">Borderless</b-form-checkbox>
      <b-form-checkbox inline v-model="outlined">Outlined</b-form-checkbox>
      <b-form-checkbox inline v-model="small">Small</b-form-checkbox>
      <b-form-checkbox inline v-model="hover">Hover</b-form-checkbox>
      <b-form-checkbox inline v-model="dark">Dark</b-form-checkbox>
      <b-form-checkbox inline v-model="fixed">Fixed</b-form-checkbox>
      <b-form-checkbox inline v-model="footClone">Foot Clone</b-form-checkbox>
    </b-form-group>
    <b-table
      :striped="striped"
      :bordered="bordered"
      :borderless="borderless"
      :outlined="outlined"
      :small="small"
      :hover="hover"
      :dark="dark"
      :fixed="fixed"
      :foot-clone="footClone"
      :items="items"
      :fields="fields"
    />
  </div>
</template>

<script>
  export default {
    data() {
      return {
        fields: ['first_name', 'last_name', 'age'],
        items: [
          { age: 40, first_name: 'Dickerson', last_name: 'Macdonald' },
          { age: 21, first_name: 'Larsen', last_name: 'Shaw' },
          { age: 89, first_name: 'Geneva', last_name: 'Wilson' }
        ],
        striped: false,
        bordered: false,
        borderless: false,
        outlined: false,
        small: false,
        hover: false,
        dark: false,
        fixed: false,
        footClone: false
      }
    }
  }
</script>

<!-- table-bordered.vue -->
```

### Row Styling

You can also style every row using the `tbody-tr-class` prop

| Property       | Type                      | Description                                                                                                                                                                    |
| -------------- | ------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| `tbodyTrClass` | String, Array or Function | Classes to be applied to every row on the table. If a function is given, it will be called as `tbodyTrClass( item, type )` and it may return an `Array`, `Object` or `String`. |

**Example: Basic row styles**

```html
<template>
  <b-table :items="items" :fields="fields" :tbody-tr-class="rowClass" />
</template>

<script>
  export default {
    data() {
      return {
        fields: ['first_name', 'last_name', 'age'],
        items: [
          { age: 40, first_name: 'Dickerson', last_name: 'Macdonald', status: 'awesome' },
          { age: 21, first_name: 'Larsen', last_name: 'Shaw' },
          { age: 89, first_name: 'Geneva', last_name: 'Wilson' }
        ]
      }
    },
    methods: {
      rowClass(item, type) {
        if (!item) return
        if (item.status === 'awesome') return 'table-success'
      }
    }
  }
</script>

<!-- table-styled-row-basic.vue -->
```

## Responsive tables

Responsive tables allow tables to be scrolled horizontally with ease. Make any table responsive
across all viewports by setting the prop `responsive` to `true`. Or, pick a maximum breakpoint with
which to have a responsive table up to by setting the prop `responsive` to one of the breakpoint
values: `sm`, `md`, `lg`, or `xl`.

**Example: Always responsive table**

```html
<template>
  <b-table responsive :items="items" />
</template>

<script>
  export default {
    data() {
      return {
        items: [
          {
            'heading 1': 'table cell',
            'heading 2': 'table cell',
            'heading 3': 'table cell',
            'heading 4': 'table cell',
            'heading 5': 'table cell',
            'heading 6': 'table cell',
            'heading 7': 'table cell',
            'heading 8': 'table cell',
            'heading 9': 'table cell',
            'heading 10': 'table cell'
          },
          {
            'heading 1': 'table cell',
            'heading 2': 'table cell',
            'heading 3': 'table cell',
            'heading 4': 'table cell',
            'heading 5': 'table cell',
            'heading 6': 'table cell',
            'heading 7': 'table cell',
            'heading 8': 'table cell',
            'heading 9': 'table cell',
            'heading 10': 'table cell'
          },
          {
            'heading 1': 'table cell',
            'heading 2': 'table cell',
            'heading 3': 'table cell',
            'heading 4': 'table cell',
            'heading 5': 'table cell',
            'heading 6': 'table cell',
            'heading 7': 'table cell',
            'heading 8': 'table cell',
            'heading 9': 'table cell',
            'heading 10': 'table cell'
          }
        ]
      }
    }
  }
</script>

<!-- table-responsive.vue -->
```

**Responsive table notes:**

- _Possible vertical clipping/truncation_. Responsive tables make use of `overflow-y: hidden`, which
  clips off any content that goes beyond the bottom or top edges of the table. In particular, this
  may clip off dropdown menus and other third-party widgets.

## Stacked tables

An alternative to responsive tables, BootstrapVue includes the stacked table option (using custom
SCSS/CSS), which allow tables to be rendered in a visually stacked format. Make any table stacked
across _all viewports_ by setting the prop `stacked` to `true`. Or, alternatively, set a breakpoint
at which the table will return to normal table format by setting the prop `stacked` to one of the
breakpoint values `'sm'`, `'md'`, `'lg'`, or `'xl'`.

Column header labels will be rendered to the left of each field value using a CSS `::before` pseudo
element, with a width of 40%.

The prop `stacked` takes precedence over the `responsive` prop.

**Example: Always stacked table**

```html
<template>
  <b-table stacked :items="items" />
</template>

<script>
  export default {
    data() {
      return {
        items: [
          { age: 40, first_name: 'Dickerson', last_name: 'Macdonald' },
          { age: 21, first_name: 'Larsen', last_name: 'Shaw' },
          { age: 89, first_name: 'Geneva', last_name: 'Wilson' }
        ]
      }
    }
  }
</script>

<!-- table-stacked.vue -->
```

**Note: When the table is visually stacked:**

- The table header (and table footer) will be hidden.
- Custom rendred header slots will not be shown, rather, the fields' `label` will be used.
- The table **cannot** be sorted by clicking the rendered field labels. You will need to provide an
  external control to select the field to sort by and the sort direction. See the
  [**Sorting**](#sorting) section below for sorting control information, as well as the
  [**complete example**](#complete-example) at the bottom of this page for an example of controlling
  sorting via the use of form controls.
- The slots `top-row` and `bottom-row` will be hidden when visually stacked.
- The table caption, if provided, will always appear at the top of the table when visually stacked.
- In an always stacked table, the table header and footer, and the fixed top and bottom row slots
  will not be rendered.

## Table caption

Add an optional caption to your table via the prop `caption` or the named slot `table-caption` (the
slot takes precedence over the prop). The default Bootstrap V4 styling places the caption at the
bottom of the table:

```html
<template>
  <b-table :items="items" :fields="fields">
    <template slot="table-caption">
      This is a table caption.
    </template>
  </b-table>
</template>

<script>
  export default {
    data() {
      return {
        fields: ['first_name', 'last_name', 'age'],
        items: [
          { age: 40, first_name: 'Dickerson', last_name: 'Macdonald' },
          { age: 21, first_name: 'Larsen', last_name: 'Shaw' },
          { age: 89, first_name: 'Geneva', last_name: 'Wilson' }
        ]
      }
    }
  }
</script>

<!-- table-caption.vue -->
```

You can have the caption placed at the top of the table by setting the `caption-top` prop to `true`:

```html
<template>
  <b-table :items="items" :fields="fields" caption-top>
    <template slot="table-caption">
      This is a table caption at the top.
    </template>
  </b-table>
</template>

<script>
  export default {
    data() {
      return {
        fields: ['first_name', 'last_name', 'age'],
        items: [
          { age: 40, first_name: 'Dickerson', last_name: 'Macdonald' },
          { age: 21, first_name: 'Larsen', last_name: 'Shaw' },
          { age: 89, first_name: 'Geneva', last_name: 'Wilson' }
        ]
      }
    }
  }
</script>

<!-- table-caption-top.vue -->
```

You can also use [custom CSS](https://developer.mozilla.org/en-US/docs/Web/CSS/caption-side) to
control the caption positioning.

## Table colgroup

Use the named slot `table-colgroup` to specify `<colgroup>` and `<col>` elements for optional
grouping and styling of table columns. Note the styles available via `<col>` elements are limited.
Refer to [MDN](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/colgroup) for details and
usage of `<colgroup>`

## Table busy state

`<b-table>` provides a `busy` prop that will flag the table as busy, which you can set to `true`
just before you update your items, and then set it to `false` once you have your items. When in the
busy state, the table will have the attribute `aria-busy="true"`.

During the busy state, the table will be rendered in a "muted" look (`opacity: 0.6`), using the
following custom CSS:

```css
/* Busy table styling */
table.b-table[aria-busy='false'] {
  opacity: 1;
}
table.b-table[aria-busy='true'] {
  opacity: 0.6;
}
```

You can override this styling using your own CSS.

You may optionally provide a `table-busy` slot to show a custom loading message or spinner whenever
the table's busy state is `true`. The slot will be placed in a `<tr>` element with class
`b-table-busy-slot`, which has one single `<td>` with a `colspan` set to the number of fields.

**Example of `table-busy` slot usage:**

```html
<template>
  <div>
    <b-button @click="toggleBusy">Toggle Busy State</b-button>
    <b-table :items="items" :busy="isBusy" class="mt-3" outlined>
      <div slot="table-busy" class="text-center text-danger">
        <br /><strong>Loading...</strong><br /><br />
      </div>
    </b-table>
  </div>
</template>
<script>
  export default {
    data() {
      return {
        isBusy: false,
        items: [
          { first_name: 'Dickerson', last_name: 'MacDonald', age: 40 },
          { first_name: 'Larsen', last_name: 'Shaw', age: 21 },
          { first_name: 'Geneva', last_name: 'Wilson', age: 89 },
          { first_name: 'Jami', last_name: 'Carney', age: 38 }
        ]
      }
    },
    methods: {
      toggleBusy() {
        this.isBusy = !this.isBusy
      }
    }
  }
</script>

<!-- table-busy-slot.vue -->
```

Also see the [**Using Items Provider Functions**](#using-items-provider-functions) below for
additional informaton on the `busy` state.

**Note:** All click related and hover events, and sort-changed events will **not** be emitted when
the table is in the `busy` state.

## Custom Data Rendering

Custom rendering for each data field in a row is possible using either
[scoped slots](http://vuejs.org/v2/guide/components.html#Scoped-Slots) or formatter callback
function.

### Scoped Field Slots

Scoped slots give you greater control over how the record data appears. If you want to add an extra
field which does not exist in the records, just add it to the `fields` array, And then reference the
field(s) in the scoped slot(s).

**Example: Custom data rendering with scoped slots**

```html
<template>
  <b-table :fields="fields" :items="items">
    <!-- A virtual column -->
    <template slot="index" slot-scope="data">
      {{ data.index + 1 }}
    </template>
    <!-- A custom formatted column -->
    <template slot="name" slot-scope="data">
      {{ data.value.first }} {{ data.value.last }}
    </template>
    <!-- A virtual composite column -->
    <template slot="nameage" slot-scope="data">
      {{ data.item.name.first }} is {{ data.item.age }} years old
    </template>
  </b-table>
</template>

<script>
  export default {
    data() {
      return {
        fields: [
          // A virtual column that doesn't exist in items
          'index',
          // A column that needs custom formatting
          { key: 'name', label: 'Full Name' },
          // A regular column
          'age',
          // A regular column
          'sex',
          // A virtual column made up from two fields
          { key: 'nameage', label: 'First name and age' }
        ],
        items: [
          { name: { first: 'John', last: 'Doe' }, sex: 'Male', age: 42 },
          { name: { first: 'Jane', last: 'Doe' }, sex: 'Female', age: 36 },
          { name: { first: 'Rubin', last: 'Kincade' }, sex: 'Male', age: 73 },
          { name: { first: 'Shirley', last: 'Partridge' }, sex: 'Female', age: 62 }
        ]
      }
    }
  }
</script>

<!-- table-data-slots.vue -->
```

The slot's scope variable (`data` in the above sample) will have the following properties:

| Property         | Type     | Description                                                                                                                                                                                                 |
| ---------------- | -------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `index`          | Number   | The row number (indexed from zero) relative to the displayed rows                                                                                                                                           |
| `item`           | Object   | The entire raw record data (i.e. `items[index]`) for this row (before any formatter is applied)                                                                                                             |
| `value`          | Any      | The value for this key in the record (`null` or `undefined` if a virtual column), or the output of the field's `formatter` function (see below for for information on field `formatter` callback functions) |
| `unformatted`    | Any      | The raw value for this key in the item record (`null` or `undefined` if a virtual column), before being passed to the field's `formatter` function                                                          |
| `detailsShowing` | Boolean  | Will be `true` if the row's `row-details` scoped slot is visible. See section [**Row details support**](#row-details-support) below for additional information                                              |
| `toggleDetails`  | Function | Can be called to toggle the visibility of the rows `row-details` scoped slot. See section [**Row details support**](#row-details-support) below for additional information                                  |
| `rowSelected`    | Boolean  | Will be `true` if the row has been selected. See section [**Row select support**](#row-select-support) for additional information                                                                           |

**Notes:**

- _`index` will not always be the actual row's index number, as it is computed after filtering,
  sorting and pagination have been applied to the original table data. The `index` value will refer
  to the **displayed row number**. This number will align with the indexes from the optional
  `v-model` bound variable._

#### Displaying raw HTML

By default `b-table` escapes HTML tags in items data and results of formatter functions, if you need
to display raw HTML code in `b-table`, you should use `v-html` directive on an element in a in
scoped field slot

```html
<template>
  <b-table :items="items"><span slot="html" slot-scope="data" v-html="data.value"/></b-table>
</template>

<script>
  export default {
    data() {
      return {
        items: [
          {
            text: 'This is <i>escaped</i> content',
            html: 'This is <i>raw <strong>HTML</strong></i> <span style="color:red">content</span>'
          }
        ]
      }
    }
  }
</script>

<!-- table-html-data-slots.vue -->
```

**Warning:** Be cautious of using this to display user supplied content, **as script tags could be
injected into your page!**

### Formatter callback

One more option to customize field output is to use formatter callback function. To enable this
field's property `formatter` is used. Value of this property may be String or function reference. In
case of a String value, the function must be defined at the parent component's methods. Providing
formatter as a `Function`, it must be declared at global scope (window or as global mixin at Vue),
unless it has been bound to a `this` context.

The callback function accepts three arguments - `value`, `key`, and `item`, and should return the
formatted value as a string (HTML strings are not supported)

**Example: Custom data rendering with formatter callback function**

```html
<template>
  <b-table :fields="fields" :items="items">
    <template slot="name" slot-scope="data">
      <!-- data.value is th value after formattetd by the Formatter -->
      <a :href="`#${data.value.replace(/[^a-z]+/i,'-').toLowerCase()}`">{{ data.value }}</a>
    </template>
  </b-table>
</template>

<script>
  export default {
    data() {
      return {
        fields: [
          {
            // A column that needs custom formatting,
            // calling formatter 'fullName' in this app
            key: 'name',
            label: 'Full Name',
            formatter: 'fullName'
          },
          // A regular column
          'age',
          {
            // A regular column with custom formatter
            key: 'sex',
            formatter: value => {
              return value.charAt(0).toUpperCase()
            }
          },
          {
            // A virtual column with custom formatter
            key: 'birthYear',
            label: 'Calculated Birth Year',
            formatter: (value, key, item) => {
              return new Date().getFullYear() - item.age
            }
          }
        ],
        items: [
          { name: { first: 'John', last: 'Doe' }, sex: 'Male', age: 42 },
          { name: { first: 'Jane', last: 'Doe' }, sex: 'Female', age: 36 },
          { name: { first: 'Rubin', last: 'Kincade' }, sex: 'male', age: 73 },
          { name: { first: 'Shirley', last: 'Partridge' }, sex: 'female', age: 62 }
        ]
      }
    },
    methods: {
      fullName(value) {
        return `${value.first} ${value.last}`
      }
    }
  }
</script>

<!-- table-data-formatter.vue -->
```

## Header/Footer custom rendering via scoped slots

It is also possible to provide custom rendering for the tables `thead` and `tfoot` elements. Note by
default the table footer is not rendered unless `foot-clone` is set to `true`.

Scoped slots for the header and footer cells uses a special naming convention of `HEAD_<fieldkey>`
and `FOOT_<fieldkey>` respectively. if a `FOOT_` slot for a field is not provided, but a `HEAD_`
slot is provided, then the footer will use the `HEAD_` slot content.

```html
<b-table :fields="fields" :items="items" foot-clone>
  <template slot="name" slot-scope="data">
    <!-- A custom formatted data column cell -->
    {{ data.value.first }} {{ data.value.last }}
  </template>
  <template slot="HEAD_name" slot-scope="data">
    <!-- A custom formatted header cell for field 'name' -->
    <em>{{ data.label }}</em>
  </template>
  <template slot="FOOT_name" slot-scope="data">
    <!-- A custom formatted footer cell  for field 'name' -->
    <strong>{{ data.label }}</strong>
  </template>
</b-table>
```

The slot's scope variable (`data` in the above example) will have the following properties:

| Property | Type   | Description                                                   |
| -------- | ------ | ------------------------------------------------------------- |
| `column` | String | The fields's `key` value                                      |
| `field`  | Object | the field's object (from the `fields` prop)                   |
| `label`  | String | The fields label value (also available as `data.field.label`) |

When placing inputs, buttons, selects or links within a `HEAD_` or `FOOT_` slot, note that
`head-clicked` event will not be emitted when the input, select, textarea is clicked (unless they
are disabled). `head-clicked` will never be emitted when clicking on links or buttons inside the
scoped slots (even when disabled)

## Row select support

You can make rows selectable, by using the prop `selectable`.

Users can easily change the selecting mode by setting the `select-mode` prop.

- `multi`: each click will select/deselect the row (default mode)
- `single`: only a single row can be selected at one time
- `range`: any row clicked is selected, any other deselected. the SHIFT key selects a range of rows,
  and CTRL/CMD click will toggle the selected row.

When a table is `selectable` and the user clicks on a row, `<b-table>` will emit the `row-selected`
event, passing a single argument which is the complete list of selected items. **Treat this argument
as read-only.**

```html
<template>
  <b-form-group label="Selection mode:" label-cols-md="4">
    <b-form-select v-model="selectMode" :options="modes" class="mb-3" />
  </b-form-group>
  <b-table
    selectable
    :select-mode="selectMode"
    selectedVariant="success"
    :items="items"
    @row-selected="rowSelected"
  />
  {{ selected }}
</template>

<script>
  export default {
    data() {
      return {
        modes: ['multi', 'single', 'range'],
        items: [
          { isActive: true, age: 40, first_name: 'Dickerson', last_name: 'Macdonald' },
          { isActive: false, age: 21, first_name: 'Larsen', last_name: 'Shaw' },
          { isActive: false, age: 89, first_name: 'Geneva', last_name: 'Wilson' },
          { isActive: true, age: 38, first_name: 'Jami', last_name: 'Carney' }
        ],
        selectMode: 'multi',
        selected: []
      }
    },
    methods: {
      rowSelected(items) {
        this.selected = items
      }
    }
  }
</script>

<!-- table-selectable.vue -->
```

**Notes:**

- _Paging, filtering, or sorting will clear the selection. The `row-selected` event will be emitted
  with an empty array if needed._
- _Selected rows will have a class of `b-row-selected` added to them._
- _When the table is in `selectable` mode, all data item `<tr>` elements will be in the document tab
  sequence (`tabindex="0"`) for accesibility reasons._

## Row details support

If you would optionally like to display additional record information (such as columns not specified
in the fields definition array), you can use the scoped slot `row-details`, in combination with the
special item record Boolean property `_showDetails`.

If the record has it's `_showDetails` property set to `true`, **and** a `row-details` scoped slot
exists, a new row will be shown just below the item, with the rendered contents of the `row-details`
scoped slot.

In the scoped field slot, you can toggle the visibility of the row's `row-details` scoped slot by
calling the `toggleDetails` function passed to the field's scoped slot variable. You can use the
scoped fields slot variable `detailsShowing` to determine the visibility of the `row-details` slot.

**Note:** If manipulating the `_showDetails` property directly on the item data (i.e. not via the
`toggleDetails` function reference), the `_showDetails` propertly **must** exist in the items data
for proper reactive detection of changes to it's value. Read more about
[Vue's reactivity limitations](https://vuejs.org/v2/guide/reactivity.html#Change-Detection-Caveats).

**Available `row-details` scoped variable properties:**

| Property        | Type     | Description                                                               |
| --------------- | -------- | ------------------------------------------------------------------------- |
| `item`          | Object   | The entire row record data object                                         |
| `index`         | Number   | The current visible row number                                            |
| `fields`        | Array    | The normalized fields definition array (in the _array of objects_ format) |
| `toggleDetails` | Function | Function to toggle visibility of the row's details slot                   |

In the following example, we show two methods of toggling the visibility of the details: one via a
button, and one via a checkbox. We also have the third row row details defaulting to have details
initially showing.

```html
<template>
  <b-table :items="items" :fields="fields" striped>
    <template slot="show_details" slot-scope="row">
      <b-button size="sm" @click="row.toggleDetails" class="mr-2">
        {{ row.detailsShowing ? 'Hide' : 'Show'}} Details
      </b-button>
      <!-- As `row.showDetails` is one-way, we call the toggleDetails function on @change -->
      <b-form-checkbox @change="row.toggleDetails" v-model="row.detailsShowing">
        Details via check
      </b-form-checkbox>
    </template>
    <template slot="row-details" slot-scope="row">
      <b-card>
        <b-row class="mb-2">
          <b-col sm="3" class="text-sm-right"><b>Age:</b></b-col>
          <b-col>{{ row.item.age }}</b-col>
        </b-row>
        <b-row class="mb-2">
          <b-col sm="3" class="text-sm-right"><b>Is Active:</b></b-col>
          <b-col>{{ row.item.isActive }}</b-col>
        </b-row>
        <b-button size="sm" @click="row.toggleDetails">Hide Details</b-button>
      </b-card>
    </template>
  </b-table>
</template>

<script>
  export default {
    data() {
      return {
        fields: ['first_name', 'last_name', 'show_details'],
        items: [
          { isActive: true, age: 40, first_name: 'Dickerson', last_name: 'Macdonald' },
          { isActive: false, age: 21, first_name: 'Larsen', last_name: 'Shaw' },
          {
            isActive: false,
            age: 89,
            first_name: 'Geneva',
            last_name: 'Wilson',
            _showDetails: true
          },
          { isActive: true, age: 38, first_name: 'Jami', last_name: 'Carney' }
        ]
      }
    }
  }
</script>

<!-- table-details.vue -->
```

## Sorting

As mentioned in the [**Fields**](#fields-column-definitions-) section above, you can make columns
sortable. Clicking on a sortable column header will sort the column in ascending direction (smallest
first), while clicking on it again will switch the direction of sorting. Clicking on a non-sortable
column will clear the sorting. The prop `no-sort-reset` can be used to disable this feature.

You can control which column is pre-sorted and the order of sorting (ascending or descending). To
pre-specify the column to be sorted, set the `sort-by` prop to the field's key. Set the sort
direction by setting `sort-desc` to either `true` (for descending) or `false` (for ascending, the
default).

The props `sort-by` and `sort-desc` can be turned into _two-way_ (syncable) props by adding the
`.sync` modifier. Your bound variables will then be updated accordingly based on the current sort
critera. See the [Vue docs](http://vuejs.org/v2/guide/components.html#sync-Modifier) for details on
the `.sync` prop modifier

**Note:** The built-in `sort-compare` routine **cannot** sort virtual columns, nor sort based on the
custom rendering of the field data (formatter functions and/or scoped slots are used only for
presentation only, and do not affect the underlying data). Refer to the
[**Sort-compare routine**](#sort-compare-routine) section below for details on sorting by
presentational data.

```html
<template>
  <div>
    <b-table :sort-by.sync="sortBy" :sort-desc.sync="sortDesc" :items="items" :fields="fields">
    </b-table>
    <p>
      Sorting By: <b>{{ sortBy }}</b>, Sort Direction:
      <b>{{ sortDesc ? 'Descending' : 'Ascending' }}</b>
    </p>
  </div>
</template>

<script>
  export default {
    data() {
      return {
        sortBy: 'age',
        sortDesc: false,
        fields: [
          { key: 'last_name', sortable: true },
          { key: 'first_name', sortable: true },
          { key: 'age', sortable: true },
          { key: 'isActive', sortable: false }
        ],
        items: [
          { isActive: true, age: 40, first_name: 'Dickerson', last_name: 'Macdonald' },
          { isActive: false, age: 21, first_name: 'Larsen', last_name: 'Shaw' },
          { isActive: false, age: 89, first_name: 'Geneva', last_name: 'Wilson' },
          { isActive: true, age: 38, first_name: 'Jami', last_name: 'Carney' }
        ]
      }
    }
  }
</script>

<!-- table-sorting.vue -->
```

### Sort-Compare routine

The built-in default `sort-compare` function sorts the specified field `key` based on the data in
the underlying record object (not by the formatted value). The field value is first stringified if
it is an object, and then sorted.

The default `sort-compare` routine **cannot** sort virtual columns, nor sort based on the custom
rendering of the field data (formatter functions and/or scoped slots are used only for
presentation). For this reason, you can provide your own custom sort compare routine by passing a
function reference to the prop `sort-compare`.

The `sort-compare` routine is passed four arguments. The first two arguments (`a` and `b`) are the
record objects for the rows being compared, the third argument is the field `key` being sorted on
(`sortBy`), and the fourth argument (`sortDesc`) is the order `<b-table>` will display the records
(`true` for descending, `false` for ascending).

The routine should always return either `-1` for `a < b` , `0` for `a === b`, or `1` for `a > b`
(the fourth argument, sorting direction, should not be used, as `b-table` will handle the
direction). The routine can also return `null` to fall back to the default built-in sort-compare
routine. You can use this feature (i.e. by returning `null`) to have your custom sort-compare
routine handle only certain fields (keys) or in the special case of virtual columns.

The default sort-compare routine works similar to the following. Note the fourth argument (sorting
direction) is **not** used in the sort comparison:

```js
function sortCompare(a, b, key) {
  if (typeof a[key] === 'number' && typeof b[key] === 'number') {
    // If both compared fields are native numbers
    return a[key] < b[key] ? -1 : a[key] > b[key] ? 1 : 0
  } else {
    // Stringify the field data and use String.localeCompare
    return toString(a[key]).localeCompare(toString(b[key]), undefined, {
      numeric: true
    })
  }
}

function toString(value) {
  if (!value) {
    return ''
  } else if (value instanceof Object) {
    return keys(value)
      .sort()
      .map(key => toString(value[key]))
      .join(' ')
  }
  return String(value)
}
```

### Disable local sorting

If you want to handle sorting entirely in your app, you can disable the local sorting in `<b-table>`
by setting the prop `no-local-sorting` to true, while still maintaining the sortable header
functionality (via `sort-changed` or `context-changed` events as well as syncable props).

You can use the syncable props `sort-by.sync` and `sort-desc.sync` to detect changes in sorting
column and direction.

Also, When a sortable column header (or footer) is clicked, the event `sort-changed` will be emitted
with a single argument containing the context object of `<b-table>`. See the
[Detection of sorting change](#detection-of-sorting-change) section below for details about the
sort-changed event and the context object.

### Change sort direction

Control the order in which ascending and descending sorting is applied when a sortable column header
is clicked, by using the the `sort-direction` prop. The default value `'asc'` applies ascending sort
first. To reverse the behavior and sort in descending direction first, set it to `'desc'`.

If you don't want the sorting direction to change at all when clicking another sortable column
header, set `sort-direction` to `'last'`.

For individual column sort directions, specify the property `sortDirection` in `fields`. See the
[Complete Example](#complete-example) below for an example of using this feature.

## Filtering

Filtering, when used, is applied to the **original items** array data, and hence it is not currently
possible to filter data based on custom rendering of virtual columns.

### Built in filtering

The item's row data values are stringified (see the sorting section above for how stringification is
done) and the filter searches that stringified data (excluding any of the special properties that
begin with an underscore `_`). The stringification also includes any data not shown in the presented
columns.

With the default built-in filter function, The `filter` prop value can either be a string or a
`RegExp` object (regular expressions should _not_ have the `/g` global flag set).

If the stringified row contains the provided string value or matches the RegExp expression then it
is inclded in the displayed results.

Set the `filter` prop to `null` or the empty string to clear the current filter.

### Custom filter function

You can also use a custom filter function, by setting the prop `filter-function` to a reference of
custom filter test function. The filter function will be passed two arguments:

- the original item row record data object. **Treat this argument as read-only.**
- the content of the `filter` prop (could be a string, RegExp, array, or object)

The function should return `true` if the record matches your criteria or `false` if the record is to
be filtered out.

For proper reactive updates to the displayed data, when not filtering you should set the `filter`
prop to `null` or an emtpy string (and not an empty object or array). The filter function will not
be called when the `fitler` prop is a falsey value.

The display of the `empty-filter-text` relies on the truthyness of the `filter` prop.

**Deprecation Notice:** Passing a filter function via the `filter` prop is deprecated and should be
avoided. Use the `filter-function` prop instead.

### Filter events

When local filtering is applied, and the resultant number of items change, `<b-table>` will emit the
`filtered` event with a two arguments:

- an array reference which is the complete list of items passing the filter routine. **Treat this
  argument as read-only.**
- the number of records that passed the filter test (the length of the first argument)

Setting the prop `filter` to null or an empty string will clear local items filtering.

### Filtering notes

You can disable local filtering competely by setting the `no-local-filtering` prop to `true`.

See the [Complete Example](#complete-example) below for an example of using the `filter` feature.

## Pagination

`<b-table>` supports built in pagination of item data. You can control how many rows are displayed
at a time by setting the `per-page` prop to the maximum number of rows you would like displayed, and
use the `current-page` prop to specify which page to display (starting from page `1`). If you set
`current-page` to a value larger than the computed number of pages, then no rows will be shown.

You can use the [`<b-pagination>`](/docs/components/pagination) component in conjunction with
`<b-table>` for providing control over pagination.

Setting `per-page` to `0` (default) will disable the local items pagination feature.

## `v-model` binding

If you bind a variable to the `v-model` prop, the contents of this variable will be the currently
displayed item records (zero based index, up to `page-size` - 1). This variable (the `value` prop)
should usually be treated as readonly.

The records within the v-model are a filtered/paginated shallow copy of `items`, and hence any
changes to a record's properties in the v-model will be reflected in the original `items` array
(except when `items` is set to a provider function). Deleting a record from the v-model will **not**
remove the record from the original items array.

**Note:** _Do not bind any value directly to the `value` prop. Use the `v-model` binding._

## Table body transition support

Vue transitions and animations are optionally supported on the `<tbody>` element via the use of Vue's
`<transition-group>` component internally. Three props are available for transitions support (all three
default to undefined):

| Prop                        | Type   | Description
| --------------------------- | ------ | ----------------------------------------------------------------- |
| `tbody-transition-props`    | Object | Object of transition-group properties                             |
| `tbody-transition-handlers` | Object | Object of transition-group event handlers                         |
| `primary-key`               | String | String specifying the field to use as a unique row key (required) |

To enable transitons you need to specify `tbody-transition-props` and/or `tbody-transition-handlers`,
and must specify which field key to use as a unique key via the `primary-key` prop. Your data **must
have** a column (specified by the `primary-key` prop) that has a **unique value per row** in order for
transitions to work properly. The `primary-key` field's _value_ can either be a unique string or number.
The field specified does not need to appear in the rendered table output, but it **must** exist in each
row of your items data.

You must also provide CSS to handle your transitions (if using CSS transitions) in your project.

For more information of Vue's list rendering transitions, see the
[Vue JS official docs](https://vuejs.org/v2/guide/transitions.html#List-Move-Transitions).

In the example below, we have used the following custom CSS:

```css
table#table-transition-example .flip-list-move {
  transition: transform 1s;
}
```

```html
<template>
  <b-table id="table-transition-example"
           :items="items"
           :fields="fields"
           striped
           small
           primary-key="a"
           :tbody-transition-props="transProps"
  />
</template>

<script>
export default {
  data () {
    return {
      transProps: {
        // Transition name
        name: 'flip-list'
      },
      items: [
        {a: 2, b: 'Two', c: 'Moose'},
        {a: 1, b: 'Three', c: 'Dog'},
        {a: 3, b: 'Four', c: 'Cat'},
        {a: 4, b: 'One', c: 'Mouse'}
      ],
      fields: [
        { key: 'a', sortable: true },
        { key: 'b', sortable: true },
        { key: 'c', sortable: true }
      ]
    }
  }
}
</script>

<!-- table-transitions.vue -->
```

## Using Items Provider Functions

As mentioned under the [**Items**](#items-record-data-) prop section, it is possible to use a
function to provide the row data (items), by specifying a function reference via the `items` prop.

The provider function is called with the following signature:

```js
provider(ctx, [callback])
```

The `ctx` is the context object associated with the table state, and contains the following five
properties:

| Property      | Type                         | Description                                                                       |
| ------------- | ---------------------------- | --------------------------------------------------------------------------------- |
| `currentPage` | Number                       | The current page number (starting from 1, the value of the `current-page` prop)   |
| `perPage`     | Number                       | The maximum number of rows per page to display (the value of the `per-page` prop) |
| `filter`      | String or RegExp or Function | the value of the `Filter` prop                                                    |
| `sortBy`      | String                       | The current column key being sorted, or `null` if not sorting                     |
| `sortDesc`    | Boolean                      | The current sort direction (`true` for descending, `false` for ascending)         |
| `apiUrl`      | String                       | the value providedd to the `api-url` prop. `null` if none provided.               |

The second argument `callback` is an optional parameter for when using the callback asynchronous
method.

**Example: returning an array of data (synchronous):**

```js
function myProvider(ctx) {
  let items = []

  // perform any items processing needed

  // Must return an array
  return items || []
}
```

**Example: Using callback to return data (asynchronous):**

```js
function myProvider(ctx, callback) {
  let params = '?page=' + ctx.currentPage + '&size=' + ctx.perPage

  this.fetchData('/some/url' + params)
    .then(data => {
      // Pluck the array of items off our axios response
      let items = data.items
      // Provide the array of items to the callabck
      callback(items)
    })
    .catch(error => {
      callback([])
    })

  // Must return null or undefined to signal b-table that callback is being used
  return null
}
```

**Example: Using a Promise to return data (asynchronous):**

```js
function myProvider(ctx) {
  let promise = axios.get('/some/url?page=' + ctx.currentPage + '&size=' + ctx.perPage)

  // Must return a promise that resolves to an array of items
  return promise.then(data => {
    // Pluck the array of items off our axios response
    let items = data.items
    // Must return an array of items or an empty array if an error occurred
    return items || []
  })
}
```

### Automated table busy state

`<b-table>` automatically tracks/controls it's `busy` state when items provider functions are used,
however it also provides a `busy` prop that can be used either to override the inner `busy` state,
or to monitor `<b-table>`'s current busy state in your application using the 2-way `.sync` modifier.

**Note:** in order to allow `<b-table>` fully track it's `busy` state, the custom items provider
function should handle errors from data sources and return an empty array to `<b-table>`.

**Example: usage of busy state**

```html
<template>
  <b-table id="my-table" :busy.sync="isBusy" :items="myProvider" :fields="fields" ... />
</template>
<script>
  export defailt {
    data () {
      return {
        isBusy: false
      }
    }
    methods: {
      myProvider (ctx) {
        // Here we don't set isBusy prop, so busy state will be
        // handled by table itself
        // this.isBusy = true
        let promise = axios.get('/some/url')

        return promise.then((data) => {
          const items = data.items
          // Here we could override the busy state, setting isBusy to false
          // this.isBusy = false
          return(items)
        }).catch(error => {
          // Here we could override the busy state, setting isBusy to false
          // this.isBusy = false
          // Returning an empty array, allows table to correctly handle
          // internal busy state in case of error
          return []
        })
      }
    }
  }
</script>
```

**Notes:**

- If you manually place the table in the `busy` state, the items provider will **not** be
  called/refreshed until the `busy` state has been set to `false`.
- All click related and hover events, and sort-changed events will **not** be emitted when in the
  `busy` state (either set automatically during provider update, or when manually set).

### Provider Paging, Filtering, and Sorting

By default, the items provider function is responsible for **all paging, filtering, and sorting** of
the data, before passing it to `b-table` for display.

You can disable provider paging, filtering, and sorting (individually) by setting the following
`b-table` prop(s) to `true`:

| Prop                    | Type    | Default | Description                                                    |
| ----------------------- | ------- | ------- | -------------------------------------------------------------- |
| `no-provider-paging`    | Boolean | `false` | When `true` enables the use of `b-table` local data pagination |
| `no-provider-sorting`   | Boolean | `false` | When `true` enables the use of `b-table` local sorting         |
| `no-provider-filtering` | Boolean | `false` | When `true` enables the use of `b-table` local filtering       |

When `no-provider-paging` is `false` (default), you should only return at maximum, `perPage` number
of records.

**Notes:**

- `<b-table>` needs reference to your pagination and filtering values in order to trigger the
  calling of the provider function. So be sure to bind to the `per-page`, `current-page` and
  `filter` props on `b-table` to trigger the provider update function call (unless you have the
  respective `no-provider-*` prop set to `true`).
- The `no-local-sorting` prop has no effect when `items` is a provider function.

### Force refreshing of table data

You may also trigger the refresh of the provider function by emitting the event `refresh::table` on
`$root` with the single argument being the `id` of your `b-table`. You must have a unique ID on your
table for this to work.

```js
this.$root.$emit('bv::refresh::table', 'my-table')
```

Or by calling the `refresh()` method on the table reference

```html
<b-table ref="table" ... />
```

```js
this.$refs.table.refresh()
```

**Note:** If the table is in the `busy` state (i.e. a provider update is currently running), the
refresh will wait until the current update is completed. If there is currently a refresh pending and
a new refresh is requested, then only one refresh will occur.

### Detection of sorting change

By listening on `<b-table>` `sort-changed` event, you can detect when the sorting key and direction
have changed.

```html
<b-table @sort-changed="sortingChanged" ... />
```

The `sort-changed` event provides a single argument of the table's current state context object.
This context object has the same format as used by items provider functions.

```js
methods: {
  sortingChanged (ctx) {
    // ctx.sortBy   ==> Field key for sorting by (or null for no sorting)
    // ctx.sortDesc ==> true if sorting descending, false otherwise
  }
}
```

You can also obtain the current sortBy and sortDesc values by using the `:sort-by.sync` and
`:sort-desc.sync` two-way props respectively (see section [**Sorting**](#sorting) above for
details).

```html
<b-table :sort-by.sync="mySortBy" :sort-desc.sync="mySortDesc" ... />
```

### Server Side Rendering

Special care must be taken when using server side rendering (SSR) and an `items` provider function.
Make sure you handle any special situations that may be needed server side when fetching your data!

When `<b-table>` is mounted in the document, it will automatically trigger a provider update call.

## Table accessibility notes

When the table is in `selectable` mode, or if there is a `row-clicked` event listener registered,
all data item rows (`<tr>` elements) will be placed into the document tab sequence (via
`tabindex="0"`) to allow keyboard-only and screen reader users the ability to click the rows.

When a column (field) is sortable, the header (and footer) heading cells will also be placed into
the document tab sequence for accesibility.

Note the following row based events/actions are not considered accessible, and should only be used
if the functionality is non critical or can be provided via other means:

- `row-dblclicked`
- `row-contextmenu`
- `row-hovered`
- `row-unhovered`
- `row-middle-clicked`

Also, `row-middle-clicked` event is not supported in all browsers (i.e. IE, Safari and most mobile
browsers). When listening for `row-middle-clicked` events originating on elements that do not
support input or navigation, you will often want to explicitly prevent other default actions mapped
to the down action of the middle mouse button. On Windows this is usually autoscroll, and on macOS
and Linux this is usually clipboard paste. This can be done by preventing the default behaviour of
the `mousedown` or `pointerdown` event.

Additionally, you may need to avoid opening a system context menu after a right click. Due to timing
differences between operating systems, this too is not a preventable default behaviour of
`row-middle-clicked`. Instead, this can be done by preventing the default behaviour of the
`contextmenu` event.

## Complete Example

```html
<template>
  <b-container fluid>
    <!-- User Interface controls -->
    <b-row>
      <b-col md="6" class="my-1">
        <b-form-group label-cols-sm="3" label="Filter" class="mb-0">
          <b-input-group>
            <b-form-input v-model="filter" placeholder="Type to Search" />
            <b-input-group-append>
              <b-btn :disabled="!filter" @click="filter = ''">Clear</b-btn>
            </b-input-group-append>
          </b-input-group>
        </b-form-group>
      </b-col>
      <b-col md="6" class="my-1">
        <b-form-group label-cols-sm="3" label="Sort" class="mb-0">
          <b-input-group>
            <b-form-select v-model="sortBy" :options="sortOptions">
              <option slot="first" :value="null">-- none --</option>
            </b-form-select>
            <b-form-select :disabled="!sortBy" v-model="sortDesc" slot="append">
              <option :value="false">Asc</option> <option :value="true">Desc</option>
            </b-form-select>
          </b-input-group>
        </b-form-group>
      </b-col>
      <b-col md="6" class="my-1">
        <b-form-group label-cols-sm="3" label="Sort direction" class="mb-0">
          <b-input-group>
            <b-form-select v-model="sortDirection" slot="append">
              <option value="asc">Asc</option> <option value="desc">Desc</option>
              <option value="last">Last</option>
            </b-form-select>
          </b-input-group>
        </b-form-group>
      </b-col>
      <b-col md="6" class="my-1">
        <b-form-group label-cols-sm="3" label="Per page" class="mb-0">
          <b-form-select :options="pageOptions" v-model="perPage" />
        </b-form-group>
      </b-col>
    </b-row>

    <!-- Main table element -->
    <b-table
      show-empty
      stacked="md"
      :items="items"
      :fields="fields"
      :current-page="currentPage"
      :per-page="perPage"
      :filter="filter"
      :sort-by.sync="sortBy"
      :sort-desc.sync="sortDesc"
      :sort-direction="sortDirection"
      @filtered="onFiltered"
    >
      <template slot="name" slot-scope="row">
        {{ row.value.first }} {{ row.value.last }}
      </template>
      <template slot="isActive" slot-scope="row">
        {{ row.value ? 'Yes :)' : 'No :(' }}
      </template>
      <template slot="actions" slot-scope="row">
        <b-button size="sm" @click="info(row.item, row.index, $event.target)" class="mr-1">
          Info modal
        </b-button>
        <b-button size="sm" @click="row.toggleDetails">
          {{ row.detailsShowing ? 'Hide' : 'Show' }} Details
        </b-button>
      </template>
      <template slot="row-details" slot-scope="row">
        <b-card>
          <ul>
            <li v-for="(value, key) in row.item" :key="key">{{ key }}: {{ value }}</li>
          </ul>
        </b-card>
      </template>
    </b-table>

    <b-row>
      <b-col md="6" class="my-1">
        <b-pagination
          :total-rows="totalRows"
          :per-page="perPage"
          v-model="currentPage"
          class="my-0"
        />
      </b-col>
    </b-row>

    <!-- Info modal -->
    <b-modal id="modalInfo" @hide="resetModal" :title="modalInfo.title" ok-only>
      <pre>{{ modalInfo.content }}</pre>
    </b-modal>
  </b-container>
</template>

<script>
  const items = [
    { isActive: true, age: 40, name: { first: 'Dickerson', last: 'Macdonald' } },
    { isActive: false, age: 21, name: { first: 'Larsen', last: 'Shaw' } },
    {
      isActive: false,
      age: 9,
      name: { first: 'Mini', last: 'Navarro' },
      _rowVariant: 'success'
    },
    { isActive: false, age: 89, name: { first: 'Geneva', last: 'Wilson' } },
    { isActive: true, age: 38, name: { first: 'Jami', last: 'Carney' } },
    { isActive: false, age: 27, name: { first: 'Essie', last: 'Dunlap' } },
    { isActive: true, age: 40, name: { first: 'Thor', last: 'Macdonald' } },
    {
      isActive: true,
      age: 87,
      name: { first: 'Larsen', last: 'Shaw' },
      _cellVariants: { age: 'danger', isActive: 'warning' }
    },
    { isActive: false, age: 26, name: { first: 'Mitzi', last: 'Navarro' } },
    { isActive: false, age: 22, name: { first: 'Genevieve', last: 'Wilson' } },
    { isActive: true, age: 38, name: { first: 'John', last: 'Carney' } },
    { isActive: false, age: 29, name: { first: 'Dick', last: 'Dunlap' } }
  ]

  export default {
    data() {
      return {
        items: items,
        fields: [
          { key: 'name', label: 'Person Full name', sortable: true, sortDirection: 'desc' },
          { key: 'age', label: 'Person age', sortable: true, class: 'text-center' },
          { key: 'isActive', label: 'is Active' },
          { key: 'actions', label: 'Actions' }
        ],
        currentPage: 1,
        perPage: 5,
        totalRows: items.length,
        pageOptions: [5, 10, 15],
        sortBy: null,
        sortDesc: false,
        sortDirection: 'asc',
        filter: null,
        modalInfo: { title: '', content: '' }
      }
    },
    computed: {
      sortOptions() {
        // Create an options list from our fields
        return this.fields
          .filter(f => f.sortable)
          .map(f => {
            return { text: f.label, value: f.key }
          })
      }
    },
    methods: {
      info(item, index, button) {
        this.modalInfo.title = `Row index: ${index}`
        this.modalInfo.content = JSON.stringify(item, null, 2)
        this.$root.$emit('bv::show::modal', 'modalInfo', button)
      },
      resetModal() {
        this.modalInfo.title = ''
        this.modalInfo.content = ''
      },
      onFiltered(filteredItems) {
        // Trigger pagination to update the number of buttons/pages due to filtering
        this.totalRows = filteredItems.length
        this.currentPage = 1
      }
    }
  }
</script>

<!-- table-complete-1.vue -->
```

<!-- Component reference added automatically from component package.json -->
