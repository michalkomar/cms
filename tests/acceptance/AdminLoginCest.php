<?php


	class AdminLoginCest
	{
		/**
		 * @type mixed
		 */
		private $pageSuffix;

		public function __construct()
		{
			$this->pageSuffix = microtime();
		}

		public function _before(AcceptanceTester $I)
		{
		}

		public function _after(AcceptanceTester $I)
		{
		}

		public function LoginTest(AcceptanceTester $I)
		{
			$I->wantTo('Log-in to administration.');
			$I->amOnPage('/administration');
			$I->fillField('username', 'besir');
			$I->fillField('password', 'MitsubishiEvo8');
			$I->click('.submit');

			$I->seeInCurrentUrl('/administration');
		}

		public function CreatePageTest(AcceptanceTester $I)
		{
			$this->LoginTest($I);

			$I->amOnPage('/administration');
			$I->canSee('New page');

			$this->_createPage($I);
			$I->see('Page was created.');

			$this->_createPage($I);
			$I->see('Unique constraint failed for key "url".');

			$I->see('TestPage');
			$I->click('TestPage');

			$I->see('Danger zone', 'h3');
			$I->see('Delete page');
			$I->click('Delete page');
			$I->click('#myModal .btn-danger');
			$I->see('The page was deleted.');
		}

		public function _createPage(AcceptanceTester $I)
		{
			$I->amOnPage('/administration/pages/new-page');

			$I->see('Composed page');
			$I->see('Link');

			$I->click('Composed page');
			$I->seeInCurrentUrl('/administration/pages/composed-page');

			$I->submitForm('#frm-pageForm', [
				'keywords' => 'foo keywords',
				'description' => 'foo description',
			]);

			$I->see('Fill Page settings -> name');

			$I->fillField('#frm-pageForm input[name="name"]', 'TestPage');
			$I->click('#frm-pageForm input[name="save"]');
			$I->see('Fill Page settings -> url');

			$I->fillField('#frm-pageForm input[name="url"]', 'test-page' . $this->pageSuffix);
			$I->selectOption('[name="parent"]', '+ Root at MainMenu');
			$I->click('#frm-pageForm input[name="save"]');
		}
	}
