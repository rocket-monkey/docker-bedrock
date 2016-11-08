<?php

namespace EkAndreas\DockerBedrock;

class Env
{
    public static function evalDocker()
    {
        $docker_name = env('server.host');

        return 'eval "$(docker-machine env '.$docker_name.')" && ';
    }

    /**
     * @return Dotenv\Dotenv Bedrock Settings
     */
    public static function getDotEnv()
    {
        $dotenv = new \Dotenv\Dotenv(Helpers::getProjectDir());
        $dotenv->load();
        //$dotenv->required(['DB_NAME', 'DB_USER', 'DB_PASSWORD']);

        return $dotenv;
    }

    public static function ensure($actionType)
    {
        $envToCheck = [];
        if ($actionType == 'docker') {
            $envToCheck = [
                'container',
            ];
        }

        foreach ($envToCheck as $key => $name) {
            try {
                $value = env($name);
            } catch (\Exception $ex) {
                $value = null;
            }
            if (!$value) {
                $stage = null;
                if (input()->hasArgument('stage')) {
                    $stage = input()->getArgument('stage');
                }
                writeln("<fg=red>Environment variable '$name' is missing in '$stage', please define before running this command!</fg=red>");
                die;
            }
        }
    }
}
