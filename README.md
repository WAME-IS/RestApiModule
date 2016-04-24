# RestApiModule
Slúži na sprístupnenie dát v systéme na využitie inými aplikáciami.

## Použitie
### Annotacia na pridanie routy v repository
Ak cheme sprístupniť dáta z repository použijeme na to annotaciu pred konkretnou funkciou. Annotacia sa drží formátu http://apidocjs.com/. 
(```@api {<metoda>} <adresa> [popis]```)
Príklad ako pridať routu:
```php
/**
 * @api {get} /article Find all articles
 *
 * @param array $criteria Critera used to filter articles
 */
public function getArticles($criteria) {
	...
}
```

### Využitie inými aplikáciami
RestApi je dostupné na adrese ```http://example.com/api/[v1]/[resource]/[parameters]``` (parametre v [] niesu povinné). Ak sa nezadá verzia api používa sa najnovšia (ale je ryskantné nezadať ju v produkcii).

Množstvo informácií o RestApi sa dá nájsť root adrese api ```http://example.com/api/v1/```. Kedže systém sa skladá z pluginou nemá všade rovnaké api. Na tejto adrese je vždy zoznam dostupných resources pre aktuálnu stránku.

## Rozšírenie
### ApiRoute
Autmatický sú pridané 2 sposoby načítania ApiRoute. Prvý vytvára základnú stranku zobrazenú ak v adrese nieje žiaden resource. Zobrazuje linky na všetky ostatné dostupné adresy aj s popisom. Druhý spristupnuje údaje z všetkych repozitárou pomocou @api annotacie. Je možné pridať ďalšie spôsoby načítania rout pridaním triedy implementujucej ```Wame\RestApiModule\Loaders\RestApiLoader``` do configurácie.

```
services:
    - Your\Awesome\Loader #Create new service
    restApiApplication:
		setup:
		- addRouteLoader(@Your\Awesome\Loader) #Add service to RestApi setup
```

### Konvertovanie dát
Dáta sa pri prenose musie konvertovat do/z JSON. RestApiModule na to využíva konvertory. Sú to triedy implementujúce ```Wame\RestApiModule\DataConverter\IDataConverter``` a pridané do configurácie. Defaultne sú dostupné konvertory pre všetky základné typy (datum, ) ale aj entity. V entitach je podporovana annotacia ```@noApi``` na vynechanie premennej v api dotazoch.

```
services:
    - Your\Awesome\Converter #Create new service
    restApiDataConverter:
		setup:
		- addConverter(@Your\Awesome\Converter) #Add service to RestApi setup
```

### Poznámky
Defáultne sú dátumy vrátené systémom v ISO 8601 formáte a taktiež sa očakává pri zadávaní údajov, ale na vstupe sú podporované aj iné formáty.