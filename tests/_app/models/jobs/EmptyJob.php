<?php

declare(strict_types = 1);

namespace app\models\jobs;

use Yii;
use yii\queue\JobInterface;

/**
 * Class EmptyJob
 * @property null|string $message
 */
class EmptyJob implements JobInterface {

	public ?string $message = null;

	/**
	 * @inheritDoc
	 * @noinspection PhpUndefinedMethodInspection
	 */
	public function execute($queue) {
		Yii::$app->log($this->message);
	}

}
