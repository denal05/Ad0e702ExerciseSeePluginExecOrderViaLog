# Ad0e702ExerciseSeePluginExecOrderViaLog
An AD0-E702 certification exercise module: see the execution order of plugins via the debug log.

Usage:
```php
> bin/magento ad0e702:trigger:plugins
The demo plugins have been triggerred successfully. 
Please inspect the var/log/debug.log file.
```

According to the devdocs there are three scenarios:  
https://developer.adobe.com/commerce/php/development/components/plugins/#examples  

----

SCENARIO A

|           | Plugin A        | Plugin B        | Plugin C        |
|-----------|-----------------|-----------------|-----------------|
| sortOrder | 10              | 20              | 30              |
| before    | beforeExecute() | beforeExecute() | beforeExecute() |
| around    |                 |                 |                 |
| after     | afterExecute()  | afterExecute()  | afterExecute()  |

The following equivalent results are expected for Scenario A:
```php
Plugin A (sort order 10): beforeExecute()
Plugin B (sort order 20): beforeExecute()
Plugin C (sort order 30): beforeExecute()
::execute()
Plugin A (sort order 10): afterExecute()
Plugin B (sort order 20): afterExecute()
Plugin C (sort order 30): afterExecute()
```
The following is a relevant excerpt from `var/log/debug.log`
```php
[2025-02-15T20:16:09.013425+00:00] main.DEBUG: Denal05\Ad0e702ExerciseSeePluginExecOrderViaLog\Plugin\PluginASortOrder10::beforeExecute [] []
[2025-02-15T20:16:09.013787+00:00] main.DEBUG: Denal05\Ad0e702ExerciseSeePluginExecOrderViaLog\Plugin\PluginBSortOrder20::beforeExecute [] []
[2025-02-15T20:16:09.013985+00:00] main.DEBUG: Denal05\Ad0e702ExerciseSeePluginExecOrderViaLog\Plugin\PluginCSortOrder30::beforeExecute [] []
[2025-02-15T20:16:09.014372+00:00] main.DEBUG: Denal05\Ad0e702ExerciseSeePluginExecOrderViaLog\Plugin\PluginASortOrder10::afterExecute [] []
[2025-02-15T20:16:09.014638+00:00] main.DEBUG: Denal05\Ad0e702ExerciseSeePluginExecOrderViaLog\Plugin\PluginBSortOrder20::afterExecute [] []
[2025-02-15T20:16:09.014795+00:00] main.DEBUG: Denal05\Ad0e702ExerciseSeePluginExecOrderViaLog\Plugin\PluginCSortOrder30::afterExecute [] []
```

----

SCENARIO B (with a `callable` around)

|           | Plugin A        | Plugin D        | Plugin C        |
|-----------|-----------------|-----------------|-----------------|
| sortOrder | 10              | 20              | 30              |
| before    | beforeExecute() | beforeExecute() | beforeExecute() |
| around    |                 | aroundExecute() |                 |
| after     | afterExecute()  | afterExecute()  | afterExecute()  |

The following equivalent results are expected for Scenario B:
```php
Plugin A (sort order 10): beforeExecute()
Plugin D (sort order 20): beforeExecute()
Plugin D (sort order 20): aroundExecute() - 1st half before callable
  Plugin C (sort order 30): beforeExecute()
    ::execute()
  Plugin C (sort order 30): afterExecute()
Plugin D (sort order 20): aroundExecute() - 2nd half after callable
Plugin A (sort order 10): afterExecute()
Plugin D (sort order 20): afterExecute()
```
The following is a relevant excerpt from `var/log/debug.log`
```php
[2025-02-15T20:47:33.881321+00:00] main.DEBUG: Denal05\Ad0e702ExerciseSeePluginExecOrderViaLog\Plugin\PluginASortOrder10::beforeExecute [] []
[2025-02-15T20:47:33.881672+00:00] main.DEBUG: Denal05\Ad0e702ExerciseSeePluginExecOrderViaLog\Plugin\PluginDSortOrder20AroundWithCallable::beforeExecute [] []
[2025-02-15T20:47:33.881807+00:00] main.DEBUG: Denal05\Ad0e702ExerciseSeePluginExecOrderViaLog\Plugin\PluginDSortOrder20AroundWithCallable::aroundExecute first half [] []
[2025-02-15T20:47:33.881982+00:00] main.DEBUG: Denal05\Ad0e702ExerciseSeePluginExecOrderViaLog\Plugin\PluginCSortOrder30::beforeExecute [] []
[2025-02-15T20:47:33.882340+00:00] main.DEBUG: Denal05\Ad0e702ExerciseSeePluginExecOrderViaLog\Plugin\PluginCSortOrder30::afterExecute [] []
[2025-02-15T20:47:33.882571+00:00] main.DEBUG: Denal05\Ad0e702ExerciseSeePluginExecOrderViaLog\Plugin\PluginDSortOrder20AroundWithCallable::aroundExecute second half [] []
[2025-02-15T20:47:33.882766+00:00] main.DEBUG: Denal05\Ad0e702ExerciseSeePluginExecOrderViaLog\Plugin\PluginASortOrder10::afterExecute [] []
[2025-02-15T20:47:33.882981+00:00] main.DEBUG: Denal05\Ad0e702ExerciseSeePluginExecOrderViaLog\Plugin\PluginDSortOrder20AroundWithCallable::afterExecute [] []
```

----

SCENARIO B (without a `callable` around)  

|           | Plugin A        | Plugin E                      | Plugin C        |
|-----------|-----------------|-------------------------------|-----------------|
| sortOrder | 10              | 20                            | 30              |
| before    | beforeExecute() | beforeExecute()               | beforeExecute() |
| around    |                 | aroundExecute() - no callable |                 |
| after     | afterExecute()  | afterExecute()                | afterExecute()  |

At the present I'm getting an error that I'm passing it something unexpected:  

`There is an error in /var/www/ad0-e702/generated/code/Denal05/Ad0e702ExerciseSeePluginExecOrderViaLog/Console/Command/TriggerPluginsCommand/Interceptor.php at line: 23
Denal05\Ad0e702ExerciseSeePluginExecOrderViaLog\Console\Command\TriggerPluginsCommand\Interceptor::execute(): Return value must be of type int, Symfony\Component\Console\Input\ArgvInput returned#0 /var/www/ad0-e702/vendor/symfony/console/Command/Command.php(326): Denal05\Ad0e702ExerciseSeePluginExecOrderViaLog\Console\Command\TriggerPluginsCommand\Interceptor->execute()`

----

SCENARIO C

|           | Plugin F        | Plugin B        | Plugin G        |
|-----------|-----------------|-----------------|-----------------|
| sortOrder | 10              | 20              | 30              |
| before    | beforeExecute() | beforeExecute() | beforeExecute() |
| around    | aroundExecute() |                 | aroundExecute() |
| after     | afterExecute()  | afterExecute()  | afterExecute()  |

The following equivalent results are expected for Scenario C:
```php
Plugin F (sort order 10): beforeExecute()
Plugin F (sort order 10): aroundExecute() - 1st half before callable
  Plugin B (sort order 20): beforeExecute()
  Plugin G (sort order 30): beforeExecute()
  Plugin G (sort order 30): aroundExecute() - 1st half before callable
    ::execute()
  Plugin G (sort order 30): aroundExecute() - 2nd half after callable
  Plugin B (sort order 20): afterExecute()
  Plugin G (sort order 30): afterExecute()
Plugin F (sort order 10): aroundExecute() - 2nd half after callable
Plugin F (sort order 10): afterExecute()
```
The following is a relevant excerpt from `var/log/debug.log`
```php
TBD
```
