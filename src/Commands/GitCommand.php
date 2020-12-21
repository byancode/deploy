<?php

namespace Byancode\Deploy\Commands;

use Illuminate\Console\Command;

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
        $message = addslashes($this->option('commit') ?? config('deploy.git.commit'));
        $output = shell_exec("cd $dir && $bin commit -a -m \"$message\" && $bin push");

        echo $output . PHP_EOL;

        /* Notifica al usuario si el servidor ha terminado la conexión */
        function ssh_disconnect($reason, $message, $language)
        {
            printf("Servidor desconectado con el siguiente código [%d] y mensaje: %s\n",
                $reason, $message);
        }

        $connection = ssh2_connect(
            config('deploy.ssh.host'),
            config('deploy.ssh.port', 22),
            [
                'disconnect' => 'ssh_disconnect',
            ]
        );

        if (!$connection) {
            die('Conexión fallida' . PHP_EOL);
        }

        $success = ssh2_auth_password(
            $connection,
            config('deploy.ssh.user'),
            config('deploy.ssh.pass')
        );

        if (!$success) {
            die('Authenticate failed' . PHP_EOL);
        }

        $dir = config('deploy.ssh.path');
        $bin = config('deploy.git.bin.remote');
        $stdout_stream = ssh2_exec($connection, "cd $dir && $bin pull");
        $err_stream = ssh2_fetch_stream($stdout_stream, SSH2_STREAM_STDERR);
        //$dio_stream = ssh2_fetch_stream($stdout_stream, SSH2_STREAM_STDDIO);

        stream_set_blocking($err_stream, true);
        stream_set_blocking($dio_stream, true);

        $result_err = stream_get_contents($err_stream);
        //$result_dio = stream_get_contents($dio_stream);

        //echo $result_dio . PHP_EOL;
        echo $result_err . PHP_EOL;
    }
}