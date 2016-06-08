# Jak psát extension pro ComposePage ve WunCMS

Pomocí extensions lze ve WunCMS ovlivňovat vykreslování částí administrace a pohodlně registrovat komponenty které chceme administrovat.

Řekněme, že chceme vytvořit extension pro vykreslení obrázku na stránku. Nazveme ji třeba FullPageImage.

K tomu, abychom mohli komponentu vykreslit potřebujeme udělat následující kroky:

**Administrace**

1. Vytvoření třídy pro DI
2. Zaregistrovat tlačítko pro přidání do stránky
3. Extendovat administrační formulář
4. Zaregistrovat factory komponenty pro vykreslení v administraci
 
**Frontend**

1. Zaregistrovat factory komponenty pro vykreslení na frontendu

###Administrace

#### 1. Začneme výrobou extension třídy samotné

Každá extension si musí do ComposePresenteru (public i private) přidat factory pro svou komponentu, kterou následně vykreslujeme.
Oba presentery pro to mají připravenou metodu `setComposeComponentFactory`. Interně je v presenteru výsledkem pole servis ze kterého si presenter na základě jména extension, v následujícím příkladě *fullPageImage*, první prvek pole v druhém parametru metody `addSetup`.

```php
namespace Wunderman\CMS\FullPageImage\DI;

use Nette\DI\CompilerExtension;
use Nette\Utils\Arrays;

class FullPageImageExtension extends CompilerExtension
{
	public function loadConfiguration()
	{
		// nacteni configu pro extension
		$builder = $this->getContainerBuilder();
		$extensionConfig = $this->loadFromFile(__DIR__ . '/config.neon');
		$this->compiler->parseServices($builder, $extensionConfig, $this->name);
		
		// merge configu aplikace a konfigu extension
		$builder->parameters = Arrays::mergeTree($builder->parameters,
			Arrays::get($extensionConfig, 'parameters', []));
	}
	
	public function beforeCompile()
	{
		$builder = $this->getContainerBuilder();
		
		// pridani komponenty pro publicComposePresenter - ten je v DI zaregistrovan jako service, proto si jej muzeme vytahnout podle definice
		/**
		 * PublicModule component
		 */
		$builder->getDefinition('publicComposePresenter')->addSetup(
			'setComposeComponentFactory',
			['fullPageImage', $this->prefix('@publicFullPageImageFactory')] // fullPageImage je identifikátor komponenty (viz níže)
		);
		
		// pridani komponenty pro privateComposePresenter
		/**
		 * PrivateModule component
		 */
		$builder->getDefinition('privateComposePresenter')->addSetup(
			'setComposeComponentFactory',
			['fullPageImage', $this->prefix('@privateFullPageImageFactory')] // fullPageImage je identifikátor komponenty (viz níže)
		);
	}
}
```


**config.neon** (*samostatně stojící konfig pro extension*)

```yaml
services:
    publicFullPageImageFactory:
        class: Wunderman\CMS\FullPageImage\PublicModule\Components\FullPageImage\FullPageImage
        implement: Wunderman\CMS\FullPageImage\PublicModule\Components\FullPageImage\IFullPageImageFactory
    privateFullPageImageFactory:
        class: Wunderman\CMS\FullPageImage\PrivateModule\Components\FullPageImage\FullPageImage
        implement: Wunderman\CMS\FullPageImage\PrivateModule\Components\FullPageImage\IFullPageImageFactory
```

#### 2. Zaregistrujeme tlačítka pro přidávání komponenty na stránku
v config.neon pro extension (zpracovává [composePresenter::registerExtensionsButtons()] (/app/PrivateModule/PagesModule/presenters/ComposePresenter.php#L288))

```yaml
parameters:
	PrivateModule:
		AddButtons:
			fullPageImage: # identifikátor komponenty (viz níže)
				text: 'Add full page image'
				tooltip: ''
```


#### 3. Extendování administračního formuláře

Celá stránka pro editaci composePage je vlastně jeden velký formulář. To v praxi znamená, že po kliknutí na [Add full page image] se tento formulář extenduje a vloží příslušná šablona. Celá funkcionalita je vázaná implementací interface [ComposedPageExtension] (/app/PrivateModule/PagesModule/presenters/ComposedPageExtension.php). Ten obsahuje metody které mohou v našem případě vypadat třeba takto:

##### Metody pro přidání položky

**Metoda addItem** nám extenduje formulář pro přidávání nové položky.
```php
public function addItem(Form &$form)
{
	if(isset($form[self::ITEM_CONTAINER])) {
		unset($form[self::ITEM_CONTAINER]);
	}
	// je dobré aby každá položka našeho formuláře byla zanořena v kontaineru, vyhneme se tak možným konfliktům
	$item = $form->addContainer(self::ITEM_CONTAINER);
	$item->addHidden('itemId')->setValue(null);
	$item->addText('alt')->setValue($this->alt);
	$item->addText('anchor')->setValue($this->anchor);
	$item->addUpload('image')->addCondition(Form::FILLED)->addRule(Form::IMAGE,
			'File must be image of type jpg, png or gif.');
	$item->setValues([], TRUE);
	$item->addHidden('type')->setValue('fullPageImage'); // identifikátor komponenty (viz níže)
}
```

**Zpracování hodnot** odeslaného formuláře (ve $values jsou hodnoty containeru). Pole navrácene z metody se uloží v databízi do tabulky `compose_article_item_param` kdy klíč pole se uloží do `name` a hodnota do sloupečku `value`.
```php
public function processNew($values)
{
	$file = $this->httpRequest->getFile(self::ITEM_CONTAINER)['image'];
	return [
		'id' => $this->attachmentService->processFile($file),
		'alt' => $values['alt'],
		'anchor' => $values['anchor'],
	];
}
```

**Nastavení templaty pro přidání** nové položky se řeší návratovou hodnotou metody `getAddItemTemplate()`.
```php
public function getAddItemTemplate()
{
	return realpath(__DIR__.'/../Templates/editItem.latte');
}
```

##### Metody pro editaci položky

Metoda `editItemParams()` zajišťuje vytvoření položek pro editační formulář a naplnění vytažených dat z databáze. Kdy v druhém parametru dostaneme [entitu ComposeArticleItem] (/app/Entity/ComposeArticleItem.php) upravované položky z databáze a zavolání metody `getParams()` získáme parametry které nám původně vrátila metoda `processNew()`. 
```php
public function editItemParams(Form &$form, $editItem)
{
	$params = $this->createParamsAssocArray($editItem->getParams());
	$this->addItem($form);
	$form[self::ITEM_CONTAINER]->setDefaults([
		'itemId' => $editItem->id,
		'alt' => Arrays::get($params, 'alt', null),
		'anchor' => Arrays::get($params, 'anchor', null),
	]);
}
```

Zpracování odeslaného editačního formuláře má na starosti metoda `processEdit()`, která by měla vracet stejné pole, jako metoda `processNew()`. 
```php
public function processEdit($values, $itemParams)
{
	$file = $this->httpRequest->getFile(self::ITEM_CONTAINER)['image'];
	return [
		'id' => $file ? $this->attachmentService->processFile($file) : Arrays::get($itemParams, 'itemId', null),
		'alt' => Arrays::get($values, 'alt', null),
		'anchor' => Arrays::get($values, 'anchor', null),
	];
}
```

Nastavení templaty pro editaci položky zajišťuje metoda `getEditItemTemplate()`, pro nás se jedná o uplně totožný formulář, takže můžeme použít stejnou template.
```php
public function getEditItemTemplate()
{
	return $this->getAddItemTemplate();
}
```

Poslední metodou interface je metoda `getAnchor()` příjímající jeden argument a to entitu [ComposeArticleItem] (/app/Entity/ComposeArticleItem.php). Jedná se o položku která se vypisuje v administraci a měla by reflektovat kotvu vygenerovanou komponentou na frontendu. V našem případě je v `$params['anchor']` hodnota prvku `anchor` z navráceného pole metodou `processNew()` resp. `processEdit()`.
```php
public function getAnchor($item)
{
	$params = $this->createParamsAssocArray($item->params);
	return isset($params['anchor']) ? $params['anchor'] : false;
}
```





