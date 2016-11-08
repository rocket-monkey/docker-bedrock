<?php

namespace EkAndreas\DockerBedrock;

class Mysql extends Container implements ContainerInterface
{
    public function ensure()
    {
        $ping = Helpers::portAlive($this->ip, 3306);

        if (!$ping) {
            if ($this->exists()) {
                $this->start();
            } else {
                $this->run();
            }
        }
    }

    public function exists()
    {
        $command = Env::evalDocker()."docker inspect $this->container";
        try {
            $output = runLocally($command);

            return true;
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function run()
    {
        writeln("<comment>Run/new mysql container $this->container</comment>");

        $env = Env::getDotEnv();
        
        $db = getenv('DB_NAME');
        if (empty($db)) {
            $db = getenv('DB_DATABASE'); // Laravel support
        }

        $password = getenv('DB_PASSWORD');

        $user = getenv('DB_USER');
        if (empty($user)) {
            $user = getenv('DB_USERNAME'); // Laravel support
        }

        $version = has('mysql.version') ? get('mysql.version') : '5.6';
        $command = "docker run --name $this->container ";
        $command .= "-e MYSQL_ROOT_PASSWORD=$password ";
        $command .= "-e MYSQL_DATABASE=$db ";
        if ($user != 'root') {
            $command .= "-e MYSQL_USER=$user ";
            $command .= "-e MYSQL_PASSWORD=$password ";
        }
        $command .= "-p 3306:3306 -d mysql:$version";
        Helpers::doLocal($command);
        Helpers::waitForPort('Waiting for mysql to start', $this->ip, 3306);
    }

    public function start()
    {
        $env = Env::getDotEnv();

        $db = getenv('DB_NAME');
        if (empty($db)) {
            $db = getenv('DB_DATABASE'); // Laravel support
        }

        $db_tests = getenv('DB_NAME').'_tests';
        if (empty($db_tests)) {
            $db_tests = getenv('DB_DATABASE'); // Laravel support
        }

        $password = getenv('DB_PASSWORD');

        $user = getenv('DB_USER');
        if (empty($user)) {
            $user = getenv('DB_USERNAME'); // Laravel support
        }

        writeln("<comment>Start existing mysql $this->container</comment>");
        $command = "docker start $this->container";
        Helpers::doLocal($command);
        Helpers::waitForPort('Waiting for mysql to start', $this->ip, 3306);

        writeln("Ensures that database '$db' exists in container {$this->container}");
        $sql = "mysql -u$user -p$password -s -e ";
        $sql .= "\"CREATE DATABASE IF NOT EXISTS $db; GRANT ALL ON *.* TO '$user'@'%' IDENTIFIED BY '$password'; FLUSH PRIVILEGES;\"";
        $command = "docker exec $this->container $sql";
        Helpers::doLocal($command);

        writeln("Ensures that test database '$db_tests' exists in container {$this->container}");
        $sql = "mysql -u$user -p$password -s -e ";
        $sql .= "\"CREATE DATABASE IF NOT EXISTS {$db}_tests; GRANT ALL ON *.* TO '$user'@'%' IDENTIFIED BY ''; FLUSH PRIVILEGES;\"";
        $command = "docker exec $this->container $sql";
        Helpers::doLocal($command);
    }

    public function stop()
    {
        writeln("<comment>Stop running mysql $this->container</comment>");
        $command = "docker stop $this->container";
        Helpers::doLocal($command);
    }

    public function kill()
    {
        if ($this->exists()) {
            writeln("<comment>Kill Mysql container $this->container</comment>");
            $command = "docker rm -f $this->container";
            Helpers::doLocal($command);
        }
    }
}
