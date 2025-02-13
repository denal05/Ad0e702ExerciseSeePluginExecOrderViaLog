# Ad0e702ExerciseSeePluginExecOrderViaLog
An AD0-E702 certification exercise module: see the execution order of plugins via the debug log.

I'm getting strange results. Expected:
```php
Plugin A (sort order 10): beforeExecute()
Plugin B (sort order 20): beforeExecute()
Plugin C (sort order 30): beforeExecute()
Plugin A (sort order 10): afterExecute()
Plugin B (sort order 20): afterExecute()
Plugin C (sort order 30): afterExecute()
```
but instead I got the following results:
```php

Plugin C (sort order 30): beforeExecute()
Plugin C (sort order 30): afterExecute()
```

The following is a relevant excerpt from `var/log/debug.log`
```php
[2025-02-13T20:05:34.036029+00:00] main.DEBUG: Denal05\Ad0e702ExerciseSeePluginExecOrderViaLog\Plugin\PC30ba::beforeExecute [] []
[2025-02-13T20:05:34.589609+00:00] main.DEBUG: Denal05\Ad0e702ExerciseSeePluginExecOrderViaLog\Plugin\PC30ba::afterExecute [] []
```
