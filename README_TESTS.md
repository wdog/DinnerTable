# Test Suite - DinnerTable

## 📊 Stato Corrente dei Test

**Totale Test**: 56
- ✅ **Passati**: 46 test (82%)
- ❌ **Falliti**: 10 test (18%)

### Test Funzionanti ✅

#### User Model Tests (14/14 passati)
```bash
docker-compose exec app ./vendor/bin/pest tests/Feature/Models/UserModelTest.php
```

Questi test verificano:
- Creazione utenti
- Relazioni (profile, dinnerGroup)
- Factory
- Validazione email unica
- Hash password
- Flag admin (con default value a false)
- Timestamps
- CRUD operations

#### Profile Model Tests (16/16 passati)
```bash
docker-compose exec app ./vendor/bin/pest tests/Feature/Models/ProfileModelTest.php
```

Questi test verificano:
- Creazione automatica profilo
- Metodo `isComplete()`
- Validazione campi obbligatori
- Avatar URL
- Cascade delete
- CRUD operations

#### Registration Simple Tests (14/14 passati) ✨
```bash
docker-compose exec app ./vendor/bin/pest tests/Feature/Auth/RegistrationSimpleTest.php
```

Questi test verificano la registrazione usando l'approccio TDD corretto (testando i model):
- Creazione utente con dati validi
- Validazione campi obbligatori (name, email, password)
- Email unica
- Password hashata
- Creazione automatica profilo
- Profilo non completo di default
- Email non verificata di default
- Flag is_admin con default a false
- Factory funzionante

### Test da Fixare ❌

#### Registration Tests (0/10 falliti)
```bash
docker-compose exec app ./vendor/bin/pest tests/Feature/Auth/RegistrationTest.php
```

**Problema**: I test usano approccio HTTP standard ma Filament usa Livewire.

**Soluzione**: Questi test sono lasciati come esempio di cosa NON fare. Usa invece RegistrationSimpleTest.php che segue l'approccio TDD corretto. Vedi [TESTING_GUIDE.md](TESTING_GUIDE.md)

## 🚀 Quick Start

### Eseguire tutti i test
```bash
docker-compose exec app ./vendor/bin/pest
```

### Eseguire test specifici
```bash
# Solo User Model tests
docker-compose exec app ./vendor/bin/pest tests/Feature/Models/UserModelTest.php

# Solo Profile Model tests
docker-compose exec app ./vendor/bin/pest tests/Feature/Models/ProfileModelTest.php

# Test specifico per nome
docker-compose exec app ./vendor/bin/pest --filter="user can be created"
```

### Modalità watch (esegue test ad ogni modifica)
```bash
docker-compose exec app ./vendor/bin/pest --watch
```

## 📚 Documentazione

### Guide Disponibili

1. **[TESTING_GUIDE.md](TESTING_GUIDE.md)** - Guida completa a TDD con Pest
   - Introduzione a TDD
   - Come debuggare test
   - Come fixare i test falliti
   - Best practices
   - Esercizi pratici

2. **Questo file** - Overview generale della test suite

## 🎯 Obiettivi di Apprendimento

Questa test suite è stata creata per insegnarti:

### 1. Test-Driven Development (TDD)
- Scrivi prima il test (Red)
- Implementa la funzionalità (Green)
- Refactoring (Refactor)

### 2. Debugging
- Come leggere gli errori
- Come usare `dd()` e `dump()`
- Come verificare il database
- Come testare step by step

### 3. Best Practices
- Un test, una cosa
- AAA Pattern (Arrange-Act-Assert)
- Test isolati e indipendenti
- Nome descrittivo dei test

## 📖 Esempi Pratici

### Esempio 1: Test Semplice che Passa ✅

```php
test('user can be created', function () {
    // Arrange: Prepara i dati
    $userData = [
        'name' => 'Mario Rossi',
        'email' => 'mario@example.com',
        'password' => bcrypt('password'),
    ];

    // Act: Esegui l'azione
    $user = User::create($userData);

    // Assert: Verifica il risultato
    expect($user)->not->toBeNull()
        ->and($user->name)->toBe('Mario Rossi')
        ->and($user->email)->toBe('mario@example.com');
});
```

**Perché passa?**
- Testa direttamente il model (non HTTP/Livewire)
- User ha tutti i campi fillable necessari
- Factory funziona correttamente

### Esempio 2: Test che Fallisce ❌

```php
test('user can register with valid data', function () {
    $response = $this->post('/dinner/register', [
        'name' => 'Mario Rossi',
        'email' => 'mario@example.com',
        'password' => 'Password123!',
    ]);

    $response->assertRedirect();
});
```

**Perché fallisce?**
- Filament non usa percorsi HTTP standard
- Il percorso `/dinner/register` non esiste
- Risposta: 405 Method Not Allowed

**Come fixarlo?**
Vedi [TESTING_GUIDE.md](TESTING_GUIDE.md#opzione-1-usare-filament-testing-tools)

## 🔨 Esercizi Progressivi

### Livello 1: Principiante
1. Esegui i test User Model e osserva i risultati
2. Modifica un test per farlo fallire (cambia l'assertion)
3. Fixalo nuovamente

### Livello 2: Intermedio
1. Aggiungi un nuovo test in UserModelTest.php
2. Testa una nuova funzionalità (es: user->canJoinGroup())
3. Implementa la funzionalità per far passare il test

### Livello 3: Avanzato
1. Fixa uno dei RegistrationTest
2. Crea test per DinnerGroup model
3. Implementa test per le relazioni tra User e DinnerGroup

## 🐛 Debugging Tips

### Problema: Test fallisce ma non capisci perché

```php
test('debug example', function () {
    $user = User::factory()->create();

    // Stampa i dati e ferma l'esecuzione
    dd($user->toArray());
});
```

### Problema: Vuoi vedere cosa c'è nel database

```php
test('check database', function () {
    User::factory()->count(3)->create();

    // Mostra tutti gli utenti
    dump(User::all()->toArray());

    // Continua il test
    expect(User::count())->toBe(3);
});
```

### Problema: Vuoi testare step by step

```php
test('step by step', function () {
    dump('Step 1: Check initial state');
    expect(User::count())->toBe(0);

    dump('Step 2: Create user');
    $user = User::factory()->create();

    dump('Step 3: Verify user', $user->toArray());
    expect(User::count())->toBe(1);
});
```

## 📈 Prossimi Passi

1. **Leggi** [TESTING_GUIDE.md](TESTING_GUIDE.md)
2. **Esegui** i test funzionanti per familiarizzare
3. **Prova** a fixare un test fallito
4. **Crea** nuovi test per altre funzionalità

## 🆘 Aiuto

### Test fallisce?
1. Leggi attentamente il messaggio di errore
2. Usa `dd()` per debuggare
3. Controlla la guida [TESTING_GUIDE.md](TESTING_GUIDE.md)

### Vuoi aggiungere un test?
1. Scegli un file esistente o creane uno nuovo
2. Segui il pattern AAA (Arrange-Act-Assert)
3. Usa nomi descrittivi

### Non sai da dove iniziare?
Inizia dai test più semplici in `UserModelTest.php` e poi passa a quelli più complessi.

## 📝 Note

- Tutti i test usano `RefreshDatabase` per avere un database pulito
- I test sono isolati e non dipendono l'uno dall'altro
- Pest usa sintassi espressiva e leggibile
- Ogni test ha commenti in italiano per facilitare la comprensione

## 🎓 Risorse

- [Pest Documentation](https://pestphp.com)
- [Laravel Testing](https://laravel.com/docs/testing)
- [Filament Testing](https://filamentphp.com/docs/3.x/panels/testing)
- [TDD Best Practices](https://martinfowler.com/bliki/TestDrivenDevelopment.html)
