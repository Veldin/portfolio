_____
Project Portfolio website is a project made by students of the Stenden University of Applied Sciences.
_____

## Hoe gebruik je de user class.
Zo maak je de een nieuwe instantie van de gebruiker class aan:
```php
require ('classes/user.php');
$user = new User($dbc);
```

## Hoe log je de gebruiker in.
```php
if($user->login("email", "wachtwoord")){
    // De gebruiker is succesvol ingelogd.
}else{
    // De gebruik is niet succesvol ingelogd. Oorzaak kan zijn: account bestaat niet, wachtwoord komt niet overeen.
}
```

## Hoe log je de gebruiker uit.
```php
$user->logout();
```

## Hoe check je of de gebruiker is ingelogd.
```php
if($user->isLoggedIn()){
    // De gebruiker is ingelogd.
}else{
    // De gebruiker is niet ingelogd.
}
```

## Hoe registreer je de gebruiker.
```php
if($user->register()){
    // De gebruiker is succesvol geregistreerd met het email adres en wachtwoord die waren ingevoerd tijdens het aanmaken van de insantie.
}else{
    // De gebruiker is niet geregistreerd. Oorzaak kan zijn: er bestaat al een record in de database met het ingevulde email adres.
}
```

## Hoe vraag je info op van de gebruiker.
Zo maak je de een nieuwe instantie van de gebruiker class aan:
```php
$user->get()['de info die jij wilt'];
// Voorbeelden zijn: email, firstname, lastname etc.
```
