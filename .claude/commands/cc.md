# Commenta Codice

Analizza il codice selezionato o il file specificato e aggiungi commenti PHPDoc completi a tutte le classi, metodi e proprietà.

## Regole

1. Usa lo stile PHPDoc standard con `@param`, `@return`, `@var`, `@throws` dove appropriato
2. Scrivi commenti in **ITALIANO**
3. Sii conciso ma chiaro
4. Non modificare il codice, aggiungi SOLO i commenti
5. Mantieni l'indentazione e la formattazione originale
6. Per i metodi, spiega cosa fanno, non come lo fanno
7. Se un metodo override un metodo parent, indicalo con `@inheritDoc` quando appropriato

## Cosa commentare

- **Classi**: Scopo e responsabilità della classe
- **Metodi**: Cosa fa il metodo, parametri, valore di ritorno, eccezioni
- **Proprietà**: Tipo e scopo della proprietà
- **Costanti**: Significato della costante

## Esempio

Input:
```php
class UserService
{
    private $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function findById($id)
    {
        return $this->repository->find($id);
    }
}
```

Output:
```php
/**
 * Servizio per la gestione degli utenti.
 * Fornisce metodi per recuperare e manipolare dati utente.
 */
class UserService
{
    /**
     * Repository per l'accesso ai dati degli utenti.
     *
     * @var UserRepository
     */
    private $repository;

    /**
     * Inizializza il servizio utenti.
     *
     * @param UserRepository $repository Repository per gli utenti
     */
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Recupera un utente tramite il suo ID.
     *
     * @param int $id ID dell'utente da recuperare
     * @return User|null L'utente trovato o null se non esiste
     */
    public function findById($id)
    {
        return $this->repository->find($id);
    }
}
```

## Istruzioni

Analizza il codice fornito e restituisci il codice completo con i commenti aggiunti. Non aggiungere spiegazioni o testo extra, solo il codice commentato.
