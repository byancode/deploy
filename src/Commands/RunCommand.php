<?php

namespace Byancode\Deploy\Commands;

use Illuminate\Console\Command;
use phpseclib\Net\SSH2;

class RunCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deploy:run
        {--c|composer : Composer update}
        {--m|commit : GitHub commit}
        {--g|git : GitHub deploy}
        {--y|yarn : Yarn update}
        {--only : Individual options}
        {--cmd= : After CommandLine}
        {--cmd-after= : After CommandLine}
        {--cmd-before= : Before CommandLine}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public $ssh;
    public function handle()
    {
        set_time_limit(0);
        [
            $composer,
            $commit,
            $only,
            $yarn,
            $git,
            $cmd,
            $after,
            $before,
        ] = [
            $this->option('composer'),
            $this->option('commit'),
            $this->option('only'),
            $this->option('yarn'),
            $this->option('git'),
            $this->option('cmd'),
            $this->option('cmd-after'),
            $this->option('cmd-before'),
        ];

        $this->ssh = new SSH2(config('deploy.ssh.host'));
        if (!$this->ssh->login(config('deploy.ssh.user'), config('deploy.ssh.pass'))) {
            die('Conexión fallida' . PHP_EOL);
        }

        $dir = config('deploy.ssh.path');
        echo $this->ssh->read('~$');
        $this->ssh->write("cd $dir" . PHP_EOL);

        if (empty($before) === false) {
            echo $this->ssh->read('~$');
            $this->ssh->write($before . PHP_EOL);
        }

        if (
            !$composer &&
            !$commit &&
            !$yarn &&
            !$only &&
            !$git
        ) {
            $this->git();
        } else {
            $git && $this->git();
            $yarn && $this->yarn();
            $composer && $this->composer();
        }

        if (empty($cmd) === false) {
            echo $this->ssh->read('~$');
            $this->ssh->write($cmd . PHP_EOL);
        }

        if (empty($after) === false) {
            echo $this->ssh->read('~$');
            $this->ssh->write($after . PHP_EOL);
        }

        echo $this->ssh->read();
    }
    public function composer()
    {
        echo $this->ssh->read('~$');
        $bin = config('deploy.composer.bin');
        $this->ssh->write("$bin update" . PHP_EOL);
    }
    public function yarn()
    {
        echo $this->ssh->read('~$');
        $bin = config('deploy.yarn.bin');
        $this->ssh->write($bin . PHP_EOL);
    }
    public function git()
    {
        $dir = base_path();
        $bin = config('deploy.git.bin.local');
        $message = $this->option('commit');
        $message = addslashes($message ? $message : config('deploy.git.commit'));
        $output = shell_exec("cd \"$dir\" && \"$bin\" add . && \"$bin\" commit -a -m \"$message\"");
        $output .= PHP_EOL;
        $output .= shell_exec("cd \"$dir\" && \"$bin\" push");

        echo $output . PHP_EOL;

        $bin = config('deploy.git.bin.remote');
        echo $this->ssh->read('~$');
        $this->ssh->write("$bin fetch" . PHP_EOL);
        echo $this->ssh->read('~$');
        $this->ssh->write("$bin reset --hard HEAD" . PHP_EOL);
        echo $this->ssh->read('~$');
        $this->ssh->write("$bin clean -f -d" . PHP_EOL);
        echo $this->ssh->read('~$');
        $this->ssh->write("$bin pull" . PHP_EOL);
    }

}