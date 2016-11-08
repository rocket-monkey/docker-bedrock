<?php

namespace EkAndreas\DockerBedrock;

class Machine implements ContainerInterface
{
    protected $ip;
    protected $name;

    public function __construct()
    {
        $this->name = env('server.host');
    }

    public function ensure()
    {
        writeln('Ensure docker-machine '.$this->name);

        if (!$this->exists()) {
            $this->run();
        }

        $command = "docker-machine status $this->name";
        $output = Helpers::doLocal($command);

        if (!preg_match('#Running#i', $output, $matches)) {
            $this->start();
        }

        $this->ip = Helpers::getMachineIp();
    }

    public function run()
    {
        writeln("<comment>Create Docker machine $this->name</comment>");
        $output = runLocally("docker-machine create -d virtualbox $this->name", 999);
        writeln("<comment>Docker-machine $this->name created</comment>");
        $this->ip = Helpers::getMachineIp();
    }

    public function kill()
    {
    }

    public function exists()
    {
        $command = 'docker-machine ls';
        $output = runLocally($command);
        if (preg_match('/'.$this->name.'/i', $output, $matches)) {
            return true;
        } else {
            return false;
        }
    }

    public function start()
    {
        $output = runLocally("docker-machine start $this->name", 999);
        writeln("<comment>Starting docker-machine $this->name, please wait...</comment>");
        sleep(20);
    }

    public function stop()
    {
        try {
            writeln("Stop docker-machine $this->name");
            $output = runLocally("docker-machine stop $this->name");
            writeln("<comment>Docker-machine $this->name stopped</comment>");
        } catch (Exception $ex) {
        }
    }
}
