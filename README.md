Phulp Assetic
=============

It's a third-party project that wraps [Assetic](https://github.com/kriswallsmith/assetic)
so that you can take advantage of its file processing features in a Phulp pipe.

## Installation

```bash
composer require nsrosenqvist/phulp-assetic
```

## Usage

```php
<?php

use NSRosenqvist\Phulp\Assetic;

$phulp->task('styles', function ($phulp) {
    $phulp->src(['assets/styles/'], '/scss$/')
        ->pipe(new Assetic([
            new \Assetic\Filter\ScssphpFilter,
            new \Assetic\Filter\CssMinFilter,
        ], 'theme.css')) // <!---
        ->pipe($phulp->dest('dist/styles/'));
});
```

First argument is an array of all the filters you want to run on the files. If
you pass a string as the second argument all the files will be concatenated and
processed in one batch. If you omit the second argument then all filters will run
on each file individually.

An easy way to add customize the filter instance is to create it in a self-executing
function. Any element of the array that's not of the type `\Asset\Filter\FilterInterface`
will be ignored.

```php
<?php

use NSRosenqvist\Phulp\Assetic;

$phulp->src(['assets/images/'], '/jpg$/')
    ->pipe(new Assetic((function() {
        if ($bin_path = shell_exec('which jpegoptim') ?: false) {
            $jpegoptim = new \Assetic\Filter\JpegoptimFilter($bin_path);
            $jpegoptim->setMax(85);
            return $jpegoptim;
        }
      })()))
      ->pipe($phulp->dest('dist/images/'));
});
```

## License
MIT
