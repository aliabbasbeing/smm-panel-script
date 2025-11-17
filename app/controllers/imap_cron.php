<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Imap_cron extends CI_Controller
{
    private $requiredToken;
    private $minInterval;
    private $lockFile;

    public function __construct()
    {
        parent::__construct();
        $this->config->load('imap_cron');

        $this->requiredToken = trim($this->config->item('imap_cron_token'));
        $this->minInterval   = (int)$this->config->item('imap_cron_min_interval');
        $this->lockFile      = $this->config->item('imap_cron_lock_file') ?: APPPATH.'cache/imap_cron_last_run.lock';

        // Load OLD-STYLE library
        $this->load->library('ImapAutoVerifier');
    }

    public function run()
    {
        // Load cron logger
        $this->load->library('cron_logger');
        $this->cron_logger->start('/imap-auto-verify');
        
        $token = $this->input->get('token', true);
        if (!$token || !$this->requiredToken || !hash_equals($this->requiredToken, $token)) {
            $this->cron_logger->log_failure('Invalid or missing token', 403);
            show_404();
        }

        // Rate limit
        if ($this->minInterval > 0 && $this->lockFile) {
            $now = time();
            if (file_exists($this->lockFile)) {
                $last = (int)trim(@file_get_contents($this->lockFile));
                if ($last && ($now - $last) < $this->minInterval) {
                    $response = [
                        'status'          => 'rate_limited',
                        'retry_after_sec' => $this->minInterval - ($now - $last),
                        'time'            => date('c')
                    ];
                    $this->cron_logger->log_rate_limited(json_encode($response), 429);
                    return $this->respond($response);
                }
            }
            @file_put_contents($this->lockFile, (string)$now);
        }

        $ok = $this->imapautoverifier->run(); // NOTE: property auto-lowercased by CI loader

        if ($ok) {
            $this->cron_logger->log_success('IMAP auto-verify completed', 200);
        } else {
            $this->cron_logger->log_failure('IMAP auto-verify failed', 500);
        }

        return $this->respond([
            'status' => $ok ? 'ok' : 'fail',
            'time'   => date('c')
        ]);
    }

    private function respond($data)
    {
        $this->output
             ->set_content_type('application/json')
             ->set_output(json_encode($data));
    }
}