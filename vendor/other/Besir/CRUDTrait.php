<?php

namespace Besir;

/**
 * Trait for CRUD operations
 * @author Petr Besir Horáček <sirbesir@gmail.com>
 */
trait CRUDTrait
{

	public $detailLinkDestination;

	public $errorResult = array('result' => false);
	public $successResult = array('result' => true);

	/** @var string $methodSuffix Suffix for CRUD methods */
	public $methodSuffix = '';

	/** @var bool $sendResponse */
	public $sendResponse = true;

	/**
	 * @author Petr Besir Horáček <sirbesir@gmail.com>
	 * @var array $data Merged with post
	 */
	public function handleCreate($data = array())
	{
		$createMethod = 'create'.ucfirst($this->methodSuffix);
		$data = array_merge($this->getHttpRequest()->getPost(), $data);
		if ($this->getUser()->isAllowed($this->getName(), 'create'))
		{
			try
			{
				$result = $this->model->$createMethod($data);
			}
			catch ( \Exception $e )
			{
				\Tracy\Debugger::log($e);
				$this->getHttpResponse()->setCode(\Nette\Http\Response::S400_BAD_REQUEST);
				$result = $this->errorResult;
			}

			if ($this->sendResponse)
			{
				$this->sendCRUDResponse($result);
			}
			return $result;
		}
	}

	/**
	 * @author Petr Besir Horáček <sirbesir@gmail.com>
	 * @var mixed
	 */
	public function handleRead($data = null)
	{
		$readMethod = 'read'.ucfirst($this->methodSuffix);
		if ($this->getUser()->isAllowed($this->getName(), 'default'))
		{
			try
			{
				$result = $this->model->$readMethod($data);

				if ( !is_null($this->detailLinkDestination) && !is_null($result))
				{
					$this->saveGlobalState();
					array_walk($result, function(&$row){
						$row['detailLink'] = $this->link($this->detailLinkDestination, array('id' => $row['id']));
					});
				}
			}
			catch ( \Exception $e )
			{
				\Tracy\Debugger::log($e);
				$this->getHttpResponse()->setCode(\Nette\Http\Response::S400_BAD_REQUEST);
				$result = $this->errorResult;
			}

			if ($this->sendResponse)
			{
				$this->sendCRUDResponse($result);
			}
			return $result;
		}
	}

	/**
	 * @author Petr Besir Horáček <sirbesir@gmail.com>
	 */
	public function handleUpdate($data = null)
	{
		$updateMethod = 'update'.ucfirst($this->methodSuffix);
		$updateData = $data ? $data : $this->getHttpRequest()->getPost();
		if ($this->getUser()->isAllowed($this->getName(), 'update'))
		{
			try
			{
				$result = $this->model->$updateMethod($updateData);
			}
			catch ( \Exception $e )
			{
				\Tracy\Debugger::log($e);
				$this->getHttpResponse()->setCode(\Nette\Http\Response::S400_BAD_REQUEST);
				$result = array($e->getMessage());
			}

			if ($this->sendResponse)
			{
				$this->sendCRUDResponse($result);
			}
			return $result;
		}
	}

	/**
	 * @author Petr Besir Horáček <sirbesir@gmail.com>
	 */
	public function handleDestroy()
	{
		$destroyMethod = 'destroy'.ucfirst($this->methodSuffix);
		$data = $this->getHttpRequest()->getPost();
		if ($this->getUser()->isAllowed($this->getName(), 'destroy'))
		{
			try
			{
				$result = $this->model->$destroyMethod((int) $data['id']);
			}
			catch ( \Exception $e )
			{
				\Tracy\Debugger::log($e);
				$this->getHttpResponse()->setCode(\Nette\Http\Response::S400_BAD_REQUEST);
				$result = $this->errorResult;
			}

			if ($this->sendResponse)
			{
				$this->sendCRUDResponse($result);
			}
			return $result;
		}
	}

	/**
	 * @author Petr Besir Horacek <sirbesir@gmail.com>
	 * @param mixed $result
	 */
	private function sendCRUDResponse($result)
	{
		$result = ($result === true) ? $this->successResult : $result;
		$this->sendJson((!is_null($result) && $result !== false) ? $result : array());
	}
}
