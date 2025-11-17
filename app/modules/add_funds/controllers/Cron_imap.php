<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Imap_cron extends CI_Controller
{
    private $requiredToken;
    private $minInterval = 0;
    private $lockFile;

    public function __construct()
    {
        parent::__construct();
        $this->config->load('imap_cron', true);
        $this->requiredToken = $this->config->item('imap_cron_token');
        $this->minInterval   = $this->config->item('imap_cron_min_interval') ?? 0;
        $this->lockFile      = $this->config->item('imap_cron_lock_file') ?? APPPATH.'cache/imap_cron_last_run.lock';

        $this->load->library('ImapAutoVerifier');
        $this->load->library('cron_logger');
    }

    public function run()
    {
        // Start logging
        $this->cron_logger->start('imap-auto-verify');
        
        // Simple debug banner
        header('X-Debug-Controller: Imap_cron');

        $token = $this->input->get('token', true);

        if (!$token) {
            $this->cron_logger->fail('Token not supplied', 401);
            return $this->debugRespond('missing_token', 'Token not supplied');
        }
        if (!$this->requiredToken) {
            $this->cron_logger->fail('Config token not loaded', 500);
            return $this->debugRespond('missing_config', 'Config token not loaded');
        }
        if (!hash_equals($this->requiredToken, $token)) {
            log_message('error', 'IMAP cron bad token');
            $this->cron_logger->fail('Token mismatch', 403);
            return $this->debugRespond('bad_token', 'Token mismatch');
        }

        if ($this->minInterval > 0 && $this->lockFile) {
            $now = time();
            if (file_exists($this->lockFile)) {
                $last = (int)trim(@file_get_contents($this->lockFile));
                if ($last && ($now - $last) < $this->minInterval) {
                    $this->cron_logger->rate_limit('Rate limited. Retry after ' . ($this->minInterval - ($now - $last)) . ' seconds');
                    return $this->debugRespond('rate_limited', 'Try later', [
                        'retry_after_sec' => $this->minInterval - ($now - $last)
                    ]);
                }
            }
            @file_put_contents($this->lockFile, (string)$now);
        }

        try {
            $ok = $this->imapautoverifier->run();
            
            if ($ok) {
                $this->cron_logger->end('IMAP auto-verification completed successfully', 200);
            } else {
                $this->cron_logger->fail('IMAP auto-verification failed', 500);
            }

            return $this->debugRespond($ok ? 'ok' : 'fail', 'Run completed');
        } catch (Exception $e) {
            $this->cron_logger->fail($e->getMessage());
            return $this->debugRespond('error', $e->getMessage());
        }
    }

    private function debugRespond($status, $msg, $extra = [])
    {
        $payload = array_merge([
            'status' => $status,
            'message'=> $msg,
            'time'   => date('c'),
            'expected_token_prefix' => substr((string)$this->requiredToken,0,8),
        ], $extra);

        $this->output
             ->set_content_type('application/json')
             ->set_output(json_encode($payload));
    }
}