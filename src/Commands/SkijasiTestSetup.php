<?php

namespace NadzorServera\Skijasi\Commands;

use Illuminate\Console\Command;

class SkijasiTestSetup extends Command
{
    public static $PHPUNIT_XML_PATH = 'phpunit.xml';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'skijasi-test:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish xml directory to file ./project/phpunit.xml';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $phpunit_xml_path = base_path(self::$PHPUNIT_XML_PATH);

        $phpunit_xml_content = <<<'XML'
        <?xml version="1.0" encoding="UTF-8"?>
        <phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="./vendor/phpunit/phpunit/phpunit.xsd" bootstrap="vendor/autoload.php" colors="true">
            <testsuites>
                <testsuite name="Unit">
                    <directory suffix="Test.php">./tests/Unit</directory>
                    <directory suffix="Test.php">./vendor/skijasi/core/tests/Unit</directory>
                </testsuite>
                <testsuite name="Feature">
                    <directory suffix="Test.php">./tests/Feature</directory>
                    <directory suffix="Test.php">./vendor/skijasi/core/tests/Feature</directory>
                </testsuite>
            </testsuites>
            <coverage processUncoveredFiles="true">
                <include>
                    <!-- <directory suffix=".php">./app</directory> -->
                    <directory suffix=".php">./vendor/skijasi/core/src/Commands</directory>
                    <directory suffix=".php">./vendor/skijasi/core/src/Controllers</directory>
                    <directory suffix=".php">./vendor/skijasi/core/src/ContentManager</directory>
                    <directory suffix=".php">./vendor/skijasi/core/src/Events</directory>
                    <directory suffix=".php">./vendor/skijasi/core/src/Exceptions</directory>
                    <directory suffix=".php">./vendor/skijasi/core/src/Helpers</directory>
                    <directory suffix=".php">./vendor/skijasi/core/src/Listeners</directory>
                    <directory suffix=".php">./vendor/skijasi/core/src/Mail</directory>
                    <directory suffix=".php">./vendor/skijasi/core/src/Middleware</directory>
                    <directory suffix=".php">./vendor/skijasi/core/src/Models</directory>
                    <directory suffix=".php">./vendor/skijasi/core/src/OrchestratorHandlers</directory>
                    <directory suffix=".php">./vendor/skijasi/core/src/Providers</directory>
                    <directory suffix=".php">./vendor/skijasi/core/src/Routes</directory>
                    <directory suffix=".php">./vendor/skijasi/core/src/Traits</directory>
                    <directory suffix=".php">./vendor/skijasi/core/src/Widgets</directory>
                    <directory suffix=".php">./vendor/skijasi/core/src/Skijasi.php</directory>
                </include>
            </coverage>
            <logging>
                <log type="coverage-clover" target="clover.xml"/>
            </logging>
            <php>
                <server name="APP_ENV" value="testing"/>
                <server name="BCRYPT_ROUNDS" value="4"/>
                <server name="CACHE_DRIVER" value="array"/>
                <!-- <server name="DB_CONNECTION" value="sqlite"/> -->
                <!-- <server name="DB_DATABASE" value=":memory:"/> -->
                <server name="MAIL_MAILER" value="array"/>
                <server name="QUEUE_CONNECTION" value="sync"/>
                <server name="SESSION_DRIVER" value="array"/>
                <server name="TELESCOPE_ENABLED" value="false"/>
            </php>
        </phpunit>
        XML;

        file_put_contents($phpunit_xml_path, $phpunit_xml_content);
    }
}
