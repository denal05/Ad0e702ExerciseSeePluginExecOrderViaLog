# Ad0e702ExerciseSeePluginExecOrderViaLog
An AD0-E702 certification exercise module: see the execution order of plugins via the debug log.

I'm getting strange results. According to the devdocs there are three scenarios:  
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
\Magento\Cms\Controller\Index\Index::execute()
Plugin A (sort order 10): afterExecute()
Plugin B (sort order 20): afterExecute()
Plugin C (sort order 30): afterExecute()
```
but instead I got the following results:
```php

Plugin C (sort order 30): beforeExecute()
\Magento\Cms\Controller\Index\Index::execute()
Plugin C (sort order 30): afterExecute()
```
The following is a relevant excerpt from `var/log/debug.log`
```php
[2025-02-13T20:25:30.678408+00:00] main.DEBUG: Denal05\Ad0e702ExerciseSeePluginExecOrderViaLog\Plugin\PluginCSortOrder30::beforeExecute [] []
[2025-02-13T20:25:31.197424+00:00] main.DEBUG: Denal05\Ad0e702ExerciseSeePluginExecOrderViaLog\Plugin\PluginCSortOrder30::afterExecute [] []
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
    \Magento\Cms\Controller\Index\Index::execute()
  Plugin C (sort order 30): afterExecute()
Plugin D (sort order 20): aroundExecute() - 2nd half after callable
Plugin A (sort order 10): afterExecute()
Plugin D (sort order 20): afterExecute()
```
but instead I got the following results:
```php

Plugin D (sort order 20): beforeExecute()
Plugin D (sort order 20): aroundExecute() - 1st half before callable
Plugin D (sort order 20): aroundExecute() - 2nd half after callable
Plugin D (sort order 20): afterExecute()
```
The following is a relevant excerpt from `var/log/debug.log`
```php
[2025-02-14T08:00:07.504464+00:00] main.DEBUG: Denal05\Ad0e702ExerciseSeePluginExecOrderViaLog\Plugin\PluginDSortOrder20AroundWithCallable::beforeExecute [] []
[2025-02-14T08:00:07.504817+00:00] main.DEBUG: Denal05\Ad0e702ExerciseSeePluginExecOrderViaLog\Plugin\PluginDSortOrder20AroundWithCallable::aroundExecute first half [] []
[2025-02-14T08:00:08.853601+00:00] main.DEBUG: Denal05\Ad0e702ExerciseSeePluginExecOrderViaLog\Plugin\PluginDSortOrder20AroundWithCallable::aroundExecute second half [] []
[2025-02-14T08:00:08.853886+00:00] main.DEBUG: Denal05\Ad0e702ExerciseSeePluginExecOrderViaLog\Plugin\PluginDSortOrder20AroundWithCallable::afterExecute [] []
```

----

SCENARIO B (without a `callable` around)  

|           | Plugin A        | Plugin E                      | Plugin C        |
|-----------|-----------------|-------------------------------|-----------------|
| sortOrder | 10              | 20                            | 30              |
| before    | beforeExecute() | beforeExecute()               | beforeExecute() |
| around    |                 | aroundExecute() - no callable |                 |
| after     | afterExecute()  | afterExecute()                | afterExecute()  |

I'm getting an error that too few arguments are being passed to aroundExecute()

`ArgumentCountError: Too few arguments to function Denal05\Ad0e702ExerciseSeePluginExecOrderViaLog\Plugin\PluginESortOrder20AroundNoCallable::aroundExecute(), 2 passed in /var/www/ad0-e702/vendor/magento/framework/Interception/Interceptor.php on line 135 and exactly 3 expected in /var/www/ad0-e702/app/code/Denal05/Ad0e702ExerciseSeePluginExecOrderViaLog/Plugin/PluginESortOrder20AroundNoCallable.php:31`

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
    \Magento\Cms\Controller\Index\Index::execute()
  Plugin G (sort order 30): aroundExecute() - 2nd half after callable
  Plugin B (sort order 20): afterExecute()
  Plugin G (sort order 30): afterExecute()
Plugin F (sort order 10): aroundExecute() - 2nd half after callable
Plugin F (sort order 10): afterExecute()
```
but instead I got the following results:
```php

Plugin G (sort order 30): beforeExecute()
Plugin G (sort order 30): aroundExecute() - 1st half before callable
Plugin G (sort order 30): aroundExecute() - 2nd half after callable
Plugin G (sort order 30): afterExecute()
```
The following is a relevant excerpt from `var/log/debug.log`
```php
[2025-02-14T09:11:57.731581+00:00] main.DEBUG: Denal05\Ad0e702ExerciseSeePluginExecOrderViaLog\Plugin\PluginGSortOrder30AroundWithCallable::beforeExecute [] []
[2025-02-14T09:11:57.731988+00:00] main.DEBUG: Denal05\Ad0e702ExerciseSeePluginExecOrderViaLog\Plugin\PluginGSortOrder30AroundWithCallable::aroundExecute first half [] []
[2025-02-14T09:11:58.293173+00:00] main.DEBUG: Denal05\Ad0e702ExerciseSeePluginExecOrderViaLog\Plugin\PluginGSortOrder30AroundWithCallable::aroundExecute second half [] []
[2025-02-14T09:11:58.293431+00:00] main.DEBUG: Denal05\Ad0e702ExerciseSeePluginExecOrderViaLog\Plugin\PluginGSortOrder30AroundWithCallable::afterExecute [] []
```
