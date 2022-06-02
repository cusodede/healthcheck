<?php /** @noinspection EmptyClassInspection */
declare(strict_types = 1);

use yii\BaseYii;
use yii\queue\Queue;
use yii\redis\Connection;

/**
 * Yii bootstrap file.
 * Used for enhanced IDE code autocompletion.
 * Note: To avoid "Multiple Implementations" PHPStorm warning and make autocomplete faster
 * exclude or "Mark as Plain Text" vendor/yiisoft/yii2/Yii.php file
 */
class Yii extends BaseYii {
	/**
	 * @var BaseApplication the application instance
	 */
	public static $app;
}

/**
 * Class BaseApplication
 * Used for properties that are identical for both WebApplication and ConsoleApplication
 *
 * @property Queue $queue
 * @property Connection $redis
 */
abstract class BaseApplication extends yii\base\Application {

}
