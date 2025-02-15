<?php
declare(strict_types=1);

namespace Denal05\Ad0e702ExerciseSeePluginExecOrderViaLog\Plugin;

use Exception;
use Psr\Log\LoggerInterface as PsrLoggerInterface;
use Denal05\Ad0e702ExerciseSeePluginExecOrderViaLog\Console\Command\TriggerPluginsCommand;

class PluginDSortOrder20AroundWithCallable
{
    private $logger;

    public function __construct(
        PsrLoggerInterface $logger
    )
    {
        $this->logger = $logger;
    }

    public function beforeExecute(
        TriggerPluginsCommand $subject
    ) {
        try {
            $this->logger->debug(__METHOD__);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
        }
        return null;
    }

    public function aroundExecute(
        TriggerPluginsCommand $subject,
        callable $next,
        ...$args
    ) {
        try {
            $this->logger->debug(__METHOD__ . " first half");
            $result = $next(...$args);
            $this->logger->debug(__METHOD__ . " second half");
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
        }
        return $result;
    }

    public function afterExecute(
        TriggerPluginsCommand $subject,
        $result
    ) {
        try {
            $this->logger->debug(__METHOD__);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
        }
        return $result;
    }
}
