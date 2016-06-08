# Motivace

Motivací CMS bylo mít možnost poskládat si stránku z různých komponent tak jak si představuje cílový uživatel. <br>
A tak jsme to vymysleli tak, že co řádek, to komponenta :-) A ono to funguje! 1. Klient to začal používat bez jediného řádku uživatelské dokumentace (dodnes není, ale bude!). <br>

# Z čeho se to skládá

Aktuálně lze vytvářet dva typy stránek. Odkaz a stráku komponovanou (ComposePage).

## Odkaz na stránku

Jedná se o jednoduchou stránku kde uživatel jen vyplní odkaz. <br>
![Administrace odkazu na stránku] (../images/admin-page-url.png)

## ComposePage

Tato varianta je výrazně zajímavější, je v ní totiž možno přidávat všechny možné komponenty, které se instalují jednoduše jako závislosti přes composer a následně je jen zaregistrujete jako extension. A pak si je jen užíváte. <br>
![Administrace ComposePage] (../images/admin-compose-page.png)

Základním kamenem CMS je tedy [ComposePresenter] (/app/PublicModule/ComposeModule/presenters/ComposePresenter.php) (public část) resp. [ComposePresenter] (/app/PrivateModule/PagesModule/presenters/ComposePresenter.php) (administrace).

Zajímá Vás [jak psát extensions pro ComposePage] (./compose-page-extensions.md)?
