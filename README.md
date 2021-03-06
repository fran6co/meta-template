# MetaTemplate

_A library which provides adapters to different PHP templating engines_

## Install

MetaTemplate follows the PSR-0 standard for class loading, so its
classes can be loaded with every compliant autoloader, such as
`Zend_Loader` or the Symfony Classloader.

Installation is easy, just register the `MetaTemplate` namespace with
your autoloader and point it to the directory where you copied the
contents of the `lib` directory to.

## Basic Usage

Most of the time you will be consuming engines via the `Template` class.
It provides some methods, which take a filename and return a template
instance.

MetaTemplate ships with these adapters/engines by default:

 * `PHPTemplate`, mapped to `.php` and `.phtml`
 * `PhpSass`, mapped to `.sass` and `.scss`
 * `MarkdownTemplate` (requires
   [php-markdown](http://github.com/michelf/php-markdown) to be loaded), mapped to
   `.md` and `.markdown`
 * `LessTemplate` (requires Less to be installed via node), mapped to
   `.less` (Requires Symfony_Process 2)
 * `MustacheTemplate` (requires
   [phly_mustache](http://github.com/weierophinney/phly_mustache)),
   mapped to `.mustache`

The `MetaTemplate\Template` class has a static `create` method, which
creates template instances from a given path.

For example:

    <?php

    use MetaTemplate\Template;

    $template = Template::create('/path/to/foo.phtml');
    echo get_class($template);
    // => "\MetaTemplate\Template\PHPTemplate"

All templates implement the `\MetaTemplate\Template\TemplateInterface`,
which provides a `render` method which, you probably guessed it, returns
the rendered contents.

The `render` method takes two arguments, which are _both_ optional:

 1. `$context`: The template's context, in most engines this is what
    `$this` inside the template script refers to.
 2. `$locals`: A array, which defines the local variables available in
    the template script.

These two arguments allow to inject the data into the template script.

If the templating engine does not have to support contexts or locals, 
these two arguments are simply ignored. This is the case with the
Markdown and Less engines.

## Digging one layer deeper

If want to setup all engine mappings up by yourself and have no
default setup of Engines, then the `MetaTemplate\Util\EngineRegistry`
is for you.

This class simply provides the instance behind the static methods of 
the `MetaTemplate\Template` class.

To map a template class to one or more file extensions, just call
the `register` method the same way you would on the `Template` class:

    $registry = new \MetaTemplate\Util\EngineRegistry;
    $registry->register('\\MetaTemplate\\Template\\LessTemplate', 'less');

You can then use the `create` method to create new Template instances
for the provided path.

## Writing your own engines

As previously noted, all templating engines need to implement the
`MetaTemplate\Template\TemplateInterface`. Though, there is the
`MetaTemplate\Template\Base` class, which you can inherit from, which
handles some redundant aspects, such as template data loading.

The `Base` class defines a `prepare` method, which lets you hook into
the template initialization. This method is called before the 
constructor returns.

Your template's content is loaded into the `$data` property.

Look at the supplied templating engines, if you need some examples.

