<?php
declare(strict_types=1);

namespace Denal05\Ad0e702ExerciseSeePluginExecOrderViaLog\Plugin;

use Exception;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

class PluginESortOrder20AroundNoCallable
{
    private $logger;

    public function __construct(
        PsrLoggerInterface $logger
    )
    {
        $this->logger = $logger;
    }

    public function beforeExecute(
        \Magento\Cms\Controller\Index\Index $subject
    ) {
        try {
            $this->logger->debug(__METHOD__);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
        }
        return null;
    }

    public function aroundExecute(
        \Magento\Cms\Controller\Index\Index $subject,
        $next,
        $result
    ) {
        try {
            $this->logger->debug(__METHOD__);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
        }
        return $result;
    }

    public function afterExecute(
        \Magento\Cms\Controller\Index\Index $subject,
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
