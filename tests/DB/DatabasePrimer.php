<?php

namespace App\Tests\DB;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Initializes database for testing.
 *
 * Will run migrations and load fixtures before each test.
 */
class DatabasePrimer
{
    public static function prime(KernelInterface $kernel): void
    {
        // Make sure we are in the test environment
        if ('test' !== $kernel->getEnvironment()) {
            throw new \LogicException('Primer must be executed in the test environment');
        }

        self::initializeDatabase($kernel);
    }

    /**
     * Create console application instance for running command silently.
     *
     * @param KernelInterface $kernel
     * @return Application
     */
    private static function createConsoleApplication(KernelInterface $kernel): Application
    {
        $application = new Application($kernel);
        $application->setAutoExit(false);
        $application->setCatchExceptions(false);

        return $application;
    }

    /**
     * Run console command as you would from command line.
     *
     * @param string $command
     * @param Application $application
     * @throws \Exception
     */
    private static function runConsoleCommand(string $command, Application $application): void
    {
        $input = new StringInput($command);
        $output = new BufferedOutput();
        $input->setInteractive(false);
        $returnCode = $application->run($input, $output);
        if (0 !== $returnCode) {
            throw new \RuntimeException('Failed to execute command. '.$output->fetch());
        }
    }

    /**
     * Initialize database by running migrations and loading fixtures.
     *
     * This executes console commands internally to best match the behaviour in development/production.
     *
     * @param KernelInterface $kernel
     * @throws \Exception
     */
    private static function initializeDatabase(KernelInterface $kernel): void
    {
        $commands = [
            'doctrine:migrations:migrate -n --env=test',
            'doctrine:fixtures:load -n --env=test',
        ];
        $application = self::createConsoleApplication($kernel);

        foreach ($commands as $command) {
            self::runConsoleCommand($command, $application);
        }
    }
}
