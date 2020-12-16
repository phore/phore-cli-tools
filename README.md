# Phore Cli Helper

Boilerplate CLI Tool


## Example

- [Example executable](doc/exampleExec);

### The Main Command

```php
class MainCmd extends PhoreAbstractMainCmd
{

    public function invoke(CliContext $context)
    {
        $opts = $context->getOpts("i:");

        $context->dispatchMap([
            "import" => new ImportCmd(),
            "search" => new SearchCmd()
        ], $opts);
    }
}
```

### The Subcommand

```php 
class SearchCmd extends PhoreAbstractCmd
{

    public function invoke(CliContext $context)
    {
        $opts = $context->getOpts();
        $context->ask("Do you want to continue?");

    }
}
```


