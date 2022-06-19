<?php

declare(strict_types=1);

namespace functional;

use dspl\healthcheck\components\web\HealthCheckAction;
use dspl\healthcheck\models\HealthCheck;
use dspl\healthcheck\models\HealthCheckInterface;
use FunctionalTester;
use Throwable;

/**
 * Тесты? Тесты!
 */
class HealthCheckCest
{

    /**
     * @param FunctionalTester $I
     * @throws Throwable
     */
    public function db(FunctionalTester $I): void
    {
        $I->amOnRoute('health/db');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContains(HealthCheckInterface::STATUS_HEALTHY);
    }

    /**
     * @param FunctionalTester $I
     * @throws Throwable
     */
    public function redis(FunctionalTester $I): void
    {
        $I->amOnRoute('health/redis');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContains(HealthCheckInterface::STATUS_HEALTHY);
    }

    /**
     * @param FunctionalTester $I
     * @throws Throwable
     */
    public function writable(FunctionalTester $I): void
    {
        $I->amOnRoute('health/writable');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContains(HealthCheckInterface::STATUS_HEALTHY);
    }

    /**
     * @param FunctionalTester $I
     * @throws Throwable
     */
    public function custom(FunctionalTester $I): void
    {
        $I->amOnRoute('health/custom');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContains(HealthCheckInterface::STATUS_HEALTHY);
    }

    /**
     * @param FunctionalTester $I
     * @throws Throwable
     */
    public function error(FunctionalTester $I): void
    {
        $I->amOnRoute('health/error');
        $I->seeResponseCodeIs(503);
        $I->seeResponseContains(HealthCheckInterface::STATUS_UNHEALTHY);
        $I->assertEquals('Something bad happened', HealthCheckAction::$LAST_ERROR);
    }
    /**
     * @param FunctionalTester $I
     * @throws Throwable
     */
    public function db_timeout(FunctionalTester $I): void
    {
        $I->amOnRoute('health/db_timeout');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContains(HealthCheckInterface::STATUS_DEGRADED);
        $I->assertStringContainsString('Execution time is too slow', HealthCheck::$DEGRADED_MESSAGE);
    }

}
