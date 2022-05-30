<?php

declare(strict_types=1);

namespace functional;

use dspl\healthcheck\components\web\HealthCheckAction;
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
        $I->seeResponseContains(HealthCheckAction::HEALTHY);
    }

    /**
     * @param FunctionalTester $I
     * @throws Throwable
     */
    public function redis(FunctionalTester $I): void
    {
        $I->amOnRoute('health/redis');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContains(HealthCheckAction::HEALTHY);
    }

    /**
     * @param FunctionalTester $I
     * @throws Throwable
     * @skip
     */
    public function writable(FunctionalTester $I): void
    {
        $I->amOnRoute('health/writable');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContains(HealthCheckAction::HEALTHY);
    }
    /**
     * @param FunctionalTester $I
     * @throws Throwable
     */
    public function custom(FunctionalTester $I): void
    {
        $I->amOnRoute('health/custom');
        $I->seeResponseCodeIs(200);
        $I->seeResponseContains(HealthCheckAction::HEALTHY);
    }

}
