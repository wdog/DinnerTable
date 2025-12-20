# Testing Guide - TDD with Pest

Questa guida ti insegnerà come usare Pest per testare l'applicazione DinnerTable seguendo il principio TDD (Test-Driven Development).

## Introduzione a TDD

Il Test-Driven Development (TDD) segue questo ciclo:
1. **Red**: Scrivi un test che fallisce
2. **Green**: Scrivi il codice minimo per far passare il test
3. **Refactor**: Migliora il codice mantenendo i test passanti

## Eseguire i Test

### Eseguire tutti i test
```bash
docker-compose exec app ./vendor/bin/pest
```

### Eseguire un singolo file di test
```bash
docker-compose exec app ./vendor/bin/pest tests/Feature/Auth/RegistrationTest.php
```

### Eseguire un singolo test
```bash
docker-compose exec app ./vendor/bin/pest --filter="user can register with valid data"
```

### Eseguire con output dettagliato
```bash
docker-compose exec app ./vendor/bin/pest --verbose
```

## Analisi dei Test Falliti

### Test 1: "user can register with valid data"

**Errore**: `Expected response status code [201, 301, 302, 303, 307, 308] but received 405`

**Cosa significa**:
- 405 = Method Not Allowed
- Il percorso `/dinner/register` non accetta richieste POST

**Come debuggare**:
```bash
# Verifica le rotte disponibili
docker-compose exec app php artisan route:list | grep register
```

**Soluzione**:
Filament usa percorsi diversi per la registrazione. Dobbiamo:
1. Trovare il percorso corretto controllando la configurazione Filament
2. Oppure usare i test Livewire di Filament invece di test HTTP standard

**Test corretto**:
```php
use function Pest\Livewire\livewire;

test('user can register with valid data', function () {
    livewire(\Filament\Pages\Auth\Register::class)
        ->fillForm([
            'name' => 'Mario Rossi',
            'email' => 'mario.rossi@example.com',
            'password' => 'Password123!',
            'passwordConfirmation' => 'Password123!',
        ])
        ->call('register')
        ->assertHasNoFormErrors();

    $this->assertAuthenticated();
});
```

### Test 2-8: Validation Tests

**Errore**: `Session is missing expected key [errors]`

**Cosa significa**:
- I test si aspettano errori di validazione ma la sessione non li contiene
- Questo perché non stiamo chiamando il form di Filament correttamente

**Come debuggare**:
```php
// Aggiungi questo per vedere la risposta effettiva
test('debug registration', function () {
    $response = $this->post('/dinner/register', []);
    dd($response->status(), $response->headers->all());
});
```

**Soluzione**:
Usare i test Livewire di Filament che gestiscono correttamente la validazione.

### Test 9: "user is redirected to profile completion"

**Errore**: `Attempt to read property "profile" on null`

**Cosa significa**:
- L'utente non è stato creato (perché il test precedente è fallito)
- `$user` è null

**Come debuggare**:
```php
test('debug user creation', function () {
    // Prova a creare manualmente
    $user = User::create([
        'name' => 'Mario Rossi',
        'email' => 'mario@example.com',
        'password' => bcrypt('password'),
    ]);

    dd($user, $user->profile);
});
```

**Soluzione**:
Prima fixare i test di registrazione precedenti.

### Test 10: "multiple users can register"

**Errore**: `Failed asserting that 0 is identical to 3`

**Cosa significa**:
- Nessun utente è stato creato
- Conseguenza dei test precedenti falliti

## Debugging Avanzato

### 1. Usare dd() per Debug Interattivo

```php
test('debug example', function () {
    $userData = ['name' => 'Test'];
    dd($userData); // Dump and die - mostra i dati e ferma l'esecuzione
});
```

### 2. Usare dump() per Debug Continuo

```php
test('debug example', function () {
    dump('Prima della chiamata'); // Mostra dati ma continua
    $response = $this->post('/test', []);
    dump($response->status()); // Mostra status e continua
    $response->assertOk();
});
```

### 3. Verificare il Database

```php
test('debug database', function () {
    $user = User::factory()->create();

    // Mostra tutti gli utenti
    dump(User::all()->toArray());

    // Verifica un record specifico
    $this->assertDatabaseHas('users', [
        'email' => $user->email,
    ]);
});
```

### 4. Testare Step by Step

```php
test('step by step test', function () {
    // Step 1: Verifica stato iniziale
    expect(User::count())->toBe(0);

    // Step 2: Crea utente
    $user = User::factory()->create();

    // Step 3: Verifica creazione
    expect(User::count())->toBe(1);
    expect($user->email)->not->toBeNull();
});
```

## Come Fixare i Test

### Opzione 1: Usare Filament Testing Tools

```bash
docker-compose exec app composer require pestphp/pest-plugin-livewire --dev
```

Poi aggiorna i test per usare Livewire:

```php
use function Pest\Livewire\livewire;

test('user can register', function () {
    livewire(\Filament\Pages\Auth\Register::class)
        ->fillForm([
            'name' => 'Mario Rossi',
            'email' => 'mario@example.com',
            'password' => 'Password123!',
            'passwordConfirmation' => 'Password123!',
        ])
        ->call('register')
        ->assertHasNoFormErrors();
});
```

### Opzione 2: Testare il Model Direttamente

```php
test('user model can be created', function () {
    $user = User::create([
        'name' => 'Mario Rossi',
        'email' => 'mario@example.com',
        'password' => bcrypt('Password123!'),
    ]);

    expect($user)->not->toBeNull()
        ->and($user->name)->toBe('Mario Rossi')
        ->and($user->email)->toBe('mario@example.com');
});
```

### Opzione 3: Testare con Factory

```php
test('user factory works', function () {
    $user = User::factory()->create([
        'name' => 'Mario Rossi',
    ]);

    expect($user->name)->toBe('Mario Rossi')
        ->and($user->profile)->not->toBeNull();
});
```

## Comandi Utili

### Vedere i percorsi disponibili
```bash
docker-compose exec app php artisan route:list
```

### Pulire il database tra i test
```php
uses(RefreshDatabase::class); // Già presente nei test
```

### Eseguire solo i test che falliscono
```bash
docker-compose exec app ./vendor/bin/pest --bail
```

### Vedere coverage del codice
```bash
docker-compose exec app ./vendor/bin/pest --coverage
```

## Esercizi Pratici

### Esercizio 1: Fix del Primo Test
1. Trova il percorso corretto per la registrazione
2. Aggiorna il test per usare il percorso corretto
3. Esegui il test e verifica che passi

### Esercizio 2: Test del Model
1. Crea un nuovo test che testa solo il model User
2. Verifica che si possa creare un utente
3. Verifica che venga creato automaticamente un profilo

### Esercizio 3: Test del Profile
1. Crea test per verificare che il profilo sia incompleto di default
2. Crea test per verificare i campi obbligatori del profilo
3. Crea test per verificare il metodo `isComplete()`

## Best Practices

1. **Un test, una cosa**: Ogni test dovrebbe verificare una sola funzionalità
2. **Nome descrittivo**: Il nome del test deve spiegare cosa fa
3. **AAA Pattern**: Arrange (prepara) → Act (esegui) → Assert (verifica)
4. **Isolamento**: Ogni test deve essere indipendente dagli altri
5. **Pulizia**: Usa `RefreshDatabase` per avere un database pulito

## Prossimi Passi

1. Installa Pest Livewire Plugin
2. Aggiorna i test per usare Livewire
3. Crea test per:
   - Completamento profilo
   - Creazione gruppi
   - Join gruppo
   - Gestione membri

## Risorse

- [Pest Documentation](https://pestphp.com)
- [Filament Testing](https://filamentphp.com/docs/3.x/panels/testing)
- [Laravel Testing](https://laravel.com/docs/testing)
