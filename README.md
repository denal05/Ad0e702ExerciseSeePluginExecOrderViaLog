# Ad0e702ExerciseSeePluginExecOrderViaLog
An AD0-E702 certification exercise module: see the execution order of plugins via the debug log.

I'm getting strange results. According to Scenario A from:  
https://developer.adobe.com/commerce/php/development/components/plugins/#examples  
the following results are expected:
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
