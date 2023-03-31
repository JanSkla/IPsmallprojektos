# school-IP-DB-Viewer
Prohlížeč databáze - tvořen v rámci výuky na SŠ DELTA

pro spuštění je třeba do directory `active/config` přidat soubor `local-config.js` s obsahem

```
{
    "db": {
    "user" : "vas_user",
    "password" : "vase_heslo"
    }
}
```
popripade upravit v `confiig.js` pripojeni k databazi

poté nainstalovat potřebné packages přes `composer update`

skrze wamp server by následně mělo být možné aplikaci spustit

tvořeno pro PHP verzi 8.1
