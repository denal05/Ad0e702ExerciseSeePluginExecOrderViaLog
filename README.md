# Ad0e702ExerciseSeePluginExecOrderViaLog
An AD0-E702 certification exercise module: see the execution order of plugins via the debug log.

Usage:
```php
> bin/magento ad0e702:trigger:plugins
The demo plugins have been triggerred successfully. 
Please inspect var/log/Denal05_Ad0e702ExerciseSeePluginExecOrderViaLog/debug.log
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
The following is a relevant excerpt from `var/log/Denal05_Ad0e702ExerciseSeePluginExecOrderViaLog/debug.log`
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
The following is a relevant excerpt from `var/log/Denal05_Ad0e702ExerciseSeePluginExecOrderViaLog/debug.log`
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
The following is a relevant excerpt from `var/log/Denal05_Ad0e702ExerciseSeePluginExecOrderViaLog/debug.log`
```php
[2025-02-17T10:47:01.941232+00:00] main.DEBUG: Denal05\Ad0e702ExerciseSeePluginExecOrderViaLog\Plugin\PluginFSortOrder10AroundWithCallable::beforeExecute [] []
[2025-02-17T10:47:01.941499+00:00] main.DEBUG: Denal05\Ad0e702ExerciseSeePluginExecOrderViaLog\Plugin\PluginFSortOrder10AroundWithCallable::aroundExecute first half [] []
[2025-02-17T10:47:01.941658+00:00] main.DEBUG: Denal05\Ad0e702ExerciseSeePluginExecOrderViaLog\Plugin\PluginBSortOrder20::beforeExecute [] []
[2025-02-17T10:47:01.941815+00:00] main.DEBUG: Denal05\Ad0e702ExerciseSeePluginExecOrderViaLog\Plugin\PluginGSortOrder30AroundWithCallable::beforeExecute [] []
[2025-02-17T10:47:01.941918+00:00] main.DEBUG: Denal05\Ad0e702ExerciseSeePluginExecOrderViaLog\Plugin\PluginGSortOrder30AroundWithCallable::aroundExecute first half [] []
[2025-02-17T10:47:01.947531+00:00] main.DEBUG: Denal05\Ad0e702ExerciseSeePluginExecOrderViaLog\Plugin\PluginGSortOrder30AroundWithCallable::aroundExecute second half [] []
[2025-02-17T10:47:01.947806+00:00] main.DEBUG: Denal05\Ad0e702ExerciseSeePluginExecOrderViaLog\Plugin\PluginBSortOrder20::afterExecute [] []
[2025-02-17T10:47:01.947975+00:00] main.DEBUG: Denal05\Ad0e702ExerciseSeePluginExecOrderViaLog\Plugin\PluginGSortOrder30AroundWithCallable::afterExecute [] []
[2025-02-17T10:47:01.948133+00:00] main.DEBUG: Denal05\Ad0e702ExerciseSeePluginExecOrderViaLog\Plugin\PluginFSortOrder10AroundWithCallable::aroundExecute second half [] []
[2025-02-17T10:47:01.948290+00:00] main.DEBUG: Denal05\Ad0e702ExerciseSeePluginExecOrderViaLog\Plugin\PluginFSortOrder10AroundWithCallable::afterExecute [] []
```

Now, why is that? What governs this weird plugin execution order?  
After some careful observation, it seems that three rules emerge, in the following order of importance:  
1) The lower the sort order, the higher the priority.  
2) The "before" methods come first, then the "around", and finally the "after" methods
3) The "around" methods encapsulate the rest of the plugins

This explains all three scenarios. Let's take a look at scenarios A and B with callable:  

Scenario A

```php
Plugin A (sort order 10): beforeExecute()
Plugin B (sort order 20): beforeExecute()
Plugin C (sort order 30): beforeExecute()
::execute()
Plugin A (sort order 10): afterExecute()
Plugin B (sort order 20): afterExecute()
Plugin C (sort order 30): afterExecute()
```
- Since the most important rule is that the plugin with the lowest sort order number has the highest priority, Plugin A executes first.
- Furthermore, since "before" comes, well, before the "after", the "before" method of Plugin A executes first.
- The rest is history.

Scenario B

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
- Plugin A has the lowest sort order number, i.e., the highest priority, so it executes first. Furthermore, the "before" method of Plugin A executes first.
- The next priority is Plugin D's "before" method.
- Next comes the first half of Plugin D's "around" method, which encapsulates the rest of the plugins (in this case only Plugin C).
- Then Plugin C's "before" method executes, and afterwards its "after" method.
- Now the plugin stack starts to pop: Plugin C is done, and the second half of Plugin D's "around" method finishes.
- Lastly, the "after" methods execute, but according to priority, Plugin A's "after" method comes before Plugin D's "after" method.
