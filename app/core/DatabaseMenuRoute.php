<?php

	namespace App;

	use Nette\Application\Request;
	use Nette\Application\UI\Presenter;

	/**
	 * DatabaseRoute
	 * @author Petr Besir Horacek <sirbesir@gmail.com>
	 */
	class DatabaseMenuRoute extends \Nette\Application\Routers\Route
	{

		/** @var \Kdyby\Doctrine\EntityRepository */
		private $menuItemRepository;

		/**
		 * @param \Nette\Http\IRequest $httpRequest
		 *
		 * @return mixed|Request|null|object|\stdClass
		 * @throws \Nette\Utils\JsonException
		 */
		public function match(\Nette\Http\IRequest $httpRequest)
		{
			$appRequest = parent::match($httpRequest);
			if ($appRequest !== null) {
				$requestParams = $appRequest->getParameters();
				$uri = ! empty($requestParams['uri']) ? $requestParams['uri'] : '';

				if ($uri == "") {
					$request = $this->menuItemRepository->findOneBy(['homepage' => 1, 'published' => 1, 'status' => 'ok']);
				} else {
					$request = $this->menuItemRepository->findOneBy(['url' => $uri, 'published' => 1, 'status' => 'ok']);
				}

				if (! $request) {
					$request = new \stdClass();
					$request->presenter = 'Public:PageNotFound';
					$request->action = 'default';
					$request->params = '[]';
					$request->homepage = false;
				}
			} else {
				return null;
			}

			if (! $request) {
				return null;
			}

			if ($request && ! empty($request->presenter)) {
				/** @var $request */
				$presenterName = $request->presenter;

				/** @var $action presenter action */
				$action = array('action' => $request->action);

				/** @var $params array */
				$params = \Nette\Utils\Json::decode($request->params, \Nette\Utils\Json::FORCE_ARRAY);
				$requestParams = array_merge($action, $params, $requestParams);
			} else {
				return null;
			}

			$request = new \Nette\Application\Request($presenterName, $httpRequest->getMethod(), $requestParams, $httpRequest->getPost(),
				$httpRequest->getFiles(), array(\Nette\Application\Request::SECURED => $httpRequest->isSecured()));

			return $request;
		}

		/**
		 * @param Request $appRequest
		 * @param \Nette\Http\Url $refUrl
		 *
		 * @return null|string
		 * @throws \Nette\Utils\JsonException
		 */
		public function constructUrl(\Nette\Application\Request $appRequest, \Nette\Http\Url $refUrl)
		{
			parent::constructUrl($appRequest, $refUrl);

			$params = $appRequest->getParameters();

			$action = isset($params['action']) ? $params['action'] : null;
			$queryParams = [];
			if (isset($params['id'])) {
				$queryParams['id'] = (int)$params['id'];
			}
			$queryParams = \Nette\Utils\Json::encode($queryParams);

			$menuItem = $this->menuItemRepository->findOneBy(array(
				'presenter' => $appRequest->getPresenterName(),
				'params' => $queryParams,
				'action' => $action
			));

			if (! $menuItem) {
				return null;
			}

			$urlPath = $menuItem ? $menuItem->url : '';
			// @todo http ?: https
			if ($menuItem->homepage === 1) {
				$url = 'http://' . $refUrl->getAuthority() . $refUrl->getBasePath();
			} else {
				$url = 'http://' . $refUrl->getAuthority() . $refUrl->getBasePath() . $urlPath;
			}

			$sep = ini_get('arg_separator.input');
			$query = http_build_query($params, '', $sep ? $sep[0] : '&');
			if ($query != '') {
				foreach ($params as $key => $param) {
					// @todo doplnit automatické odstraňování uložených parametrů v db
					if ($key == 'action' || $key == 'id' || $key == 'uri') {
						unset($params[$key]);
					}
				}

				$query = http_build_query($params, '', $sep ? $sep[0] : '&');
				$url .= $query !== '' ? '?' . $query : '';
			}

			return $url;
		}


		// <editor-fold defaultstate="collapsed" desc="Dependency injection">
		/**
		 * MenuItemDao injection
		 *
		 * @param \Kdyby\Doctrine\EntityRepository $menuItemRepository
		 */
		public function injectMenuItemDao(\Gedmo\Tree\Entity\Repository\NestedTreeRepository $menuItemRepository)
		{
			if ($this->menuItemRepository !== null) {
				throw new \Nette\InvalidStateException('MenuItemDao has already been set.');
			}
			$this->menuItemRepository = $menuItemRepository;
		}
		// </editor-fold>
	}
