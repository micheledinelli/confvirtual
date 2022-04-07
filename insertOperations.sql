USE CONFVIRTUAL;
#Creazione di un utenti base per il popolamento del DataBase
call confvirtual.InserisciUtente('Mick', 'Michele00!', 'Michele', 'Dinelli', '2000-07-27', 'Bologna');
call confvirtual.InserisciUtente('Pino', 'Pinuccio2000', 'Youssef', 'Hanna', '2000-02-21', 'Roma');
call confvirtual.InserisciUtente('Fede', 'Albero1999', 'Federico', 'Bellati', '2000-01-11', 'Firenze');
call confvirtual.InserisciUtente('Pippo', 'Delta99', 'Filippo', 'Brajucha', '2000-10-25', 'Treviso');
call confvirtual.InserisciUtente('Jack', 'TheJack00', 'Giacomo', 'Villanese', '2000-09-5', 'Milano');
call confvirtual.InserisciUtente('Lucy', 'Fotografa49', 'Lucia', 'Bonomelli', '2000-11-20', 'Palermo');
call confvirtual.InserisciUtente('Saretta', 'Saretta2000', 'Sara', 'Brugnaro', '1999-01-15', 'Venezia');

#Assegnazione al seguente utente il ruolo di Admin
call confvirtual.CambiaRuolo('Mick', 'ADMIN');

#Assegnazione ai seguenti utenti il ruolo di Presenter
call confvirtual.CambiaRuolo('Pino', 'PRESENTER');
call confvirtual.CambiaRuolo('Fede', 'PRESENTER');

#Assegnazione ai seguenti utenti il ruolo di Speaker
call confvirtual.CambiaRuolo('Pippo', 'SPEAKER');
call confvirtual.CambiaRuolo('Jack', 'SPEAKER');

#Creazione conferenze per il popolamento del database
call confvirtual.CreaConferenzaAdmin('Teoria del calcolo', 'Mick', 'FCT', 2023, null);
call confvirtual.CreaConferenzaAdmin('Linguaggi di programmazione', 'Mick', 'ICALP', 2023, null);
call confvirtual.CreaConferenzaAdmin('Algoritmi e calcolo', 'Mick', 'ISAAC', 2023, null);
call confvirtual.CreaConferenzaAdmin('Strutture dati', 'Mick', 'WADS', 2022, null);
call confvirtual.CreaConferenzaAdmin('Basi di dati', 'Mick', 'DBC', 2022, null);

#Associazione delle date di svolgimento alle conferenze
call confvirtual.InserisciDateSvoglimento('FCT', 2023, '2023-01-01', '2023-01-05');
call confvirtual.InserisciDateSvoglimento('ICALP', 2023, '2023-02-10', '2023-02-15');
call confvirtual.InserisciDateSvoglimento('ISAAC', 2023, '2023-06-20', '2023-06-24');
call confvirtual.InserisciDateSvoglimento('WADS', 2022, '2022-09-25', '2022-09-28');
call confvirtual.InserisciDateSvoglimento('DBC', 2022, '2022-10-7', '2022-10-12');

#Inserimento sessioni per le conferenze
call confvirtual.CreaSessione('FCT', 'Macchina di Turing', 2023, '2023-01-01', '09:00:00', '12:00:00', 'www.google.com');
call confvirtual.CreaSessione('FCT', 'Costo computazionale', 2023, '2023-01-03', '15:00:00', '18:00:00', 'www.google.com');
call confvirtual.CreaSessione('ICALP', 'JavaScript', 2023, '2023-02-12', '11:00:00', '14:00:00', 'www.google.com');
call confvirtual.CreaSessione('ICALP', 'PHP', 2023, '2023-02-15', '17:00:00', '19:00:00', 'www.google.com');
call confvirtual.CreaSessione('ISAAC', 'Bellman Ford', 2023, '2023-06-21', '10:00:00', '13:00:00', 'www.google.com');
call confvirtual.CreaSessione('ISAAC', 'Torre di Hanoi', 2023, '2023-06-22', '15:00:00', '15:30:00', 'www.google.com');
call confvirtual.CreaSessione('WADS', 'ALberi binari di ricerca', 2022, '2022-09-25', '9:30:00', '11:00:00', 'www.google.com');
call confvirtual.CreaSessione('WADS', 'Hash tables', 2022, '2022-09-27', '15:00:00', '16:30:00', 'www.google.com');
call confvirtual.CreaSessione('DBC', 'CRUD OPERATIONS', 2022, '2022-10-08', '10:00:00', '12:30:00', 'www.google.com');
call confvirtual.CreaSessione('DBC', 'MongoDB', 2022, '2022-10-09', '14:00:00', '16:00:00', 'www.google.com');

#Creazione di articoli e tutorial associati alle sessioni
call confvirtual.InserisciArticolo(1, '9:00:00', '12:00:00', 'Nuove macchine di Turing', 100, '');
call confvirtual.InserisciArticolo(2, '15:00:00', '16:00:00', 'Minimizzare il costo', 200, '');
call confvirtual.InserisciTutorial(2, '16:00:00', '18:00:00', 'Spazio logaritmico', 'Il concetto di riduzione ...');
call confvirtual.InserisciTutorial(3, '11:00:00', '14:00:00', 'Programmazione web', 'La programmazione web lato client ...');
call confvirtual.InserisciTutorial(4, '17:00:00', '19:00:00', 'Linguaggio di scripting', 'La programmazione di pagine web dinamiche ...');
call confvirtual.InserisciArticolo(5, '10:00:00', '11:00:00', 'Cammino minimo', 400, '');
call confvirtual.InserisciTutorial(5, '11:00:00', '13:00:00', 'Grafo', 'Gli archi di peso negativo ...');
call confvirtual.InserisciArticolo(6, '15:00:00', '15:30:00', 'Rompicapo matematico', 50, '');
call confvirtual.InserisciTutorial(7, '9:30:00', '11:00:00', 'Red and black', 'Il costo computazionale di ricerca ...');
call confvirtual.InserisciTutorial(8, '15:00:00', '16:30:00', 'Mappe in java', 'La struttura dati mette in corrispondenza ...');
call confvirtual.InserisciArticolo(9, '10:00:00', '11:00:00', 'Create', 20, '');
call confvirtual.InserisciTutorial(9, '11:00:00', '12:30:00', 'CRUD in sql', 'Le operazioni CRUD sono ...');
call confvirtual.InserisciArticolo(10, '14:00:00', '16:00:00', 'MongoDB e PHP', 20, '');

#Inserimento Autori
INSERT INTO AUTORE(Nome, Cognome) VALUES ("Youssef", "Hanna");
INSERT INTO AUTORE(Nome, Cognome) VALUES("Federico", "Bellati");
INSERT INTO AUTORE(Nome, Cognome) VALUES("Filippo", "Brajucha");

#Inserimento delle associazioni tra autori e articoli
INSERT INTO SCRITTURA(NomeAutore, CognomeAutore, CodiceArticolo) VALUES("Youssef", "Hanna", 1);
INSERT INTO SCRITTURA(NomeAutore, CognomeAutore, CodiceArticolo) VALUES("Federico", "Bellati", 2);
INSERT INTO SCRITTURA(NomeAutore, CognomeAutore, CodiceArticolo) VALUES("Youssef", "Hanna", 6);
INSERT INTO SCRITTURA(NomeAutore, CognomeAutore, CodiceArticolo) VALUES("Federico", "Bellati", 8);
INSERT INTO SCRITTURA(NomeAutore, CognomeAutore, CodiceArticolo) VALUES("Federico", "Bellati", 11);
INSERT INTO SCRITTURA(NomeAutore, CognomeAutore, CodiceArticolo) VALUES("Youssef", "Hanna", 13);
INSERT INTO SCRITTURA(NomeAutore, CognomeAutore, CodiceArticolo) VALUES("Federico", "Bellati", 13);

#Inserimento delle università convenzionate tra le quali lo speaker può scegliere la propria affiliazione
INSERT INTO UNIVERSITA(NomeUniversità, NomeDipartimento) VALUES("Unibo", "Informatica");
INSERT INTO UNIVERSITA(NomeUniversità, NomeDipartimento) VALUES("Unimi", "Lingue");
INSERT INTO UNIVERSITA(NomeUniversità, NomeDipartimento) VALUES("Unitn", "statistica");
INSERT INTO UNIVERSITA(NomeUniversità, NomeDipartimento) VALUES("Unibo", "Ingegneria");










