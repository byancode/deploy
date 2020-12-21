<?php

namespace Byancode\Deploy\Commands;

use Illuminate\Console\Command;
use phpseclib3\Net\SSH2;

class GitCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deploy:git {--m|commit}';

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
    public function handle()
    {
        set_time_limit(0);

        $dir = base_path();
        $bin = config('deploy.git.bin.local');
        $message = $this->option('commit');
        $message = addslashes($message ? $message : config('deploy.git.commit'));
        $output = shell_exec("cd \"$dir\" && \"$bin\" commit -a -m \"$message\"");
        $output .= PHP_EOL;
        $output .= shell_exec("cd \"$dir\" && \"$bin\" push");

        echo $output . PHP_EOL;

        $ssh = new SSH2(config('deploy.ssh.host'));
        if (!$ssh->login(config('deploy.ssh.user'), config('deploy.ssh.pass'))) {
            die('Conexión fallida' . PHP_EOL);
        }
        $dir = config('deploy.ssh.path');
        $bin = config('deploy.git.bin.remote');
        echo $ssh->read('~$');
        $ssh->write("cd $dir" . PHP_EOL);
        echo $ssh->read('~$');
        $ssh->write("$bin fetch" . PHP_EOL);
        echo $ssh->read('~$');
        $ssh->write("$bin pull" . PHP_EOL);
        echo $ssh->read('Username');
        $ssh->write(config('deploy.git.user'));
        echo $ssh->read('Password');
        $ssh->write(config('deploy.git.pass'));
        $ssh->read();
    }
}