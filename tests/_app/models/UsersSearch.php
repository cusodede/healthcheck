<?php

declare(strict_types = 1);

namespace app\models;

use Throwable;
use yii\data\ActiveDataProvider;

/**
 * Class UsersSearch
 */
class UsersSearch extends Users {

	/**
	 * @inheritdoc
	 */
	public function rules():array {
		return [
			[['id'], 'integer'],
			[['username', 'login', 'password'], 'safe'],
		];
	}

	/**
	 * @param array $params
	 * @return ActiveDataProvider
	 * @throws Throwable
	 */
	public function search(array $params):ActiveDataProvider {
		$query = Users::find();

		$dataProvider = new ActiveDataProvider([
			'id' => 'usersDataProvider',
			'query' => $query
		]);

		$dataProvider->setSort([
			'enableMultiSort' => true,
			//в тестах потребуется сортировка по двум атрибутам для гарантии попадания в проверяемый индекс
			'defaultOrder' => ['id' => SORT_ASC],
			'attributes' => [
				'id' => [
					'asc' => ['id' => SORT_ASC],
					'desc' => ['id' => SORT_DESC]
				],
				'username' => [
					'asc' => ['username' => SORT_ASC],
					'desc' => ['username' => SORT_DESC]
				],
				'login' => [
					'asc' => ['login' => SORT_ASC],
					'desc' => ['login' => SORT_DESC]
				]
			]
		]);

		$this->load($params);
		$query->andFilterWhere(['id' => $this->id]);
		$query->andFilterWhere(['like', 'username', $this->username]);
		$query->andFilterWhere(['like', 'login', $this->login]);

		return $dataProvider;
	}

}
