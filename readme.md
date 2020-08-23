EnvEditor
----

![PHP Composer](https://github.com/ArtARTs36/EnvEditor/workflows/Testing/badge.svg?branch=master)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
<a href="https://poser.pugx.org/artarts36/env-editor/d/total.svg">
    <img src="https://poser.pugx.org/artarts36/env-editor/d/total.svg" alt="Total Downloads">
</a>

----

### Installation:

`composer require artarts36/env-editor`

### Examples:

```php
use ArtARTs36\EnvEditor\Editor;

$env = Editor::load('.env')
    ->set('APP_NAME', 'Test APP')
    ->set('APP_ENV', 'local');

$env->save();
// or
Editor::save($env);

var_dump($env->get('APP_NAME'));
var_dump($env->get('APP_ENV'));
```
