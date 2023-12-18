# markdown2html

Converts markdown files to static html pages.
[See demo](http://christianblos.github.io/codedocs) (I used it to document another project of mine).


## Installation

Use composer to install the latest version: `composer require --dev christianblos/markdown2html`


## Create html via command line

First, you must configure markdown2html by adding a **markdown2html.config.php** file to the root folder
of your repository:

```php
<?php
$theme        = new Markdown2Html\Theme\DefaultTheme();
$theme->title = 'My Project';

$config = new \Markdown2Html\Config();

$config->src   = '/path/to/markdown-files';
$config->dest  = '/path/to/destination-folder'; 
$config->theme = $theme;

return $config;
```

Now you can execute `vendor/bin/markdown2html` to create the html files in your destination folder

> Note: If your config file is not in the root folder of your project, you can pass it as first argument:
> `vendor/bin/markdown2html /path/to/config.php`


## Create html via code

Maybe you have your own command line tool and you want to use PHP code directly to generate the html files.
This is no problem:

```php
<?php
$src   = '/path/to/markdown-files';
$dest  = '/path/to/destination-folder'; 

$theme        = new Markdown2Html\Theme\DefaultTheme();
$theme->title = 'My Project';

$builder = new Markdown2Html\Builder();
$builder->build($src, $dest, $theme);
```


## Structure of Markdown files

The navigation in the generated html is based on your folder structure.
Let's assume you have the following file structure:

```txt
markdown
   |- 00.Installation.md
   |- 01.Configuration.md
   |- 02.Usage.md
   |- 02.Usage
   |     |- 00.Via-command--line.md
   |     |- 01.Via-PHP.md
   |
   |- index.md
```

The **number prefix** (like "01.") indicates the order of navigation items. You can omit it if the order doesn't matter.

All dashes are replaced with spaces ("Via-PHP" → "Via PHP").
If you want to have dashes in the navigation, use 2 or 3 dashes:

- "00.Via-command--line.md" → "Via command-line" 
- "00.Via-command---line.md" → "Via command - line"

If there is a file **having the same name** as a folder (like 02.Usage and 02.Usage.md), it will be the index page
of this folder. If you don't have the file, the index will be created automatically and contain a sub navigation.

The content of **index.md** contains the text of the html index page.

In the example above the generated navigation will look like this:

```txt
Installation
Configuration
Usage
   Via command-line
   Via PHP
```


## Default Theme

The DefaultTheme has some additional configurations you can use:

```php
<?php
$theme                = new Markdown2Html\Theme\DefaultTheme();

// Add additional links to the navigation
$theme->naviLinks = [
    'Github' => 'https://github.com/christianblos'
];

// overwrite styles
$theme->additionalCss = 'a#header {background-color:red}';
```
