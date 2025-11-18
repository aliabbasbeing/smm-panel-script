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
        $this->load->library('cron_logger');
    }

    public function run()
    {
        // Start logging
        $log_id = $this->cron_logger->start('imap-auto-verify');
        
        $token = $this->input->get('token', true);
        if (!$token || !$this->requiredToken || !hash_equals($this->requiredToken, $token)) {
            $this->cron_logger->end($log_id, 'Failed', 403, 'Invalid or missing token');
            show_404();
        }

        // Rate limit
        if ($this->minInterval > 0 && $this->lockFile) {
            $now = time();
            if (file_exists($this->lockFile)) {
                $last = (int)trim(@file_get_contents($this->lockFile));
                if ($last && ($now - $last) < $this->minInterval) {
                    return $this->respond([
                        'status'          => 'rate_limited',
                        'retry_after_sec' => $this->minInterval - ($now - $last),
                        'time'            => date('c')
                    ]);
                }
            }
            @file_put_contents($this->lockFile, (string)$now);
        }

        $ok = $this->imapautoverifier->run(); // NOTE: property auto-lowercased by CI loader

        // Log the result
        $status = $ok ? 'Success' : 'Failed';
        $response_code = $ok ? 200 : 500;
        $message = $ok ? 'IMAP verification completed' : 'IMAP verification failed';
        $this->cron_logger->end($log_id, $status, $response_code, $message);
        
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