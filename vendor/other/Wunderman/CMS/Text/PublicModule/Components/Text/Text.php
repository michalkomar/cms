<?php

namespace App\PublicModule\Component;

/**
 * Menu
 * @author Petr Besir Horáček <sirbesir@gmail.com>
 */
class Text extends \Nette\Application\UI\Control
{
	/** @var \Kdyby\Doctrine\EntityManager */
	private $em;

	/** @var \App\Entity\TextArticle */
	private $textRepository;

	public function __construct(\Kdyby\Doctrine\EntityManager $em)
	{
		$this->em = $em;
		$this->textRepository = $em->getRepository('\App\Entity\TextArticle');
	}

	/**
	 * Render setup
	 * @author Petr Besir Horáček <sirbesir@gmail.com>
	 * @var mixed $textId
	 * @see Nette\Application\Control#render()
	 */
	public function render($textId)
	{
		$template = 'default.latte';

		if (is_int($textId))
		{
			$this->getTemplate()->article = $this->textRepository->findOneBy(array('id' => $textId));
		}
		elseif (is_array($textId))
		{
			$this->getTemplate()->article = $this->textRepository->findOneBy(array('id' => $textId['id']));

			if (isset($textId['template']))
			{
				$template = $textId['template'];
			}
		}
		elseif ($textId === 'homepage')
		{
			$this->getTemplate()->article = $this->textRepository->findOneBy(array('homepage' => 1));
		}

		$templateBase = __DIR__."/templates/";
		$templatePath = $templateBase . $template;

		if (!file_exists($templatePath))
		{
			$templatePath = $templateBase . 'default.latte';
		}

		$this->getTemplate()->setFile(__DIR__."/templates/{$template}");
		$this->getTemplate()->render();
	}
}
