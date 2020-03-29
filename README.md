# ThemePlate Hook

## Usage

```php

use ThemePlate\Hook;

Hook::append( 'sample_filter', 'test' );
Hook::append( 'sample_filter', 'test2' );
Hook::append( 'sample_filter', 'test3' );
Hook::prepend( 'sample_filter', 'test0' );
Hook::pluck( 'sample_filter', 'test2' );
Hook::replace( 'sample_filter', 'test', 'try' );
```
