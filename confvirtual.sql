DROP DATABASE IF EXISTS CONFVIRTUAL;
CREATE DATABASE CONFVIRTUAL;
USE CONFVIRTUAL;

CREATE TABLE CONFERENZA(
	Acronimo VARCHAR(10),
	AnnoEdizione INT,
    Logo BLOB,
    Svolgimento ENUM("ATTIVA","COMPLETATA"),
    NumeroPresentazion INT,
    PRIMARY KEY(Acronimo, AnnoEdizione)
) ENGINE="INNODB";

CREATE TABLE SPONSOR(
	Nome VARCHAR(20),
    Logo BLOB,
    Importo DOUBLE,
    PRIMARY KEY(Nome)
) ENGINE="INNODB";

CREATE TABLE SPONSORIZZAZIONE(
	AcronimoConferenza VARCHAR(10),
    AnnoEdizione INT,
    NomeSponsor VARCHAR(20),
    PRIMARY KEY(AcronimoConferenza, AnnoEdizione, NomeSponsor),
    FOREIGN KEY(AcronimoConferenza, AnnoEdizione) REFERENCES CONFERENZA(Acronimo, AnnoEdizione) ON DELETE CASCADE,
    FOREIGN KEY (NomeSponsor) REFERENCES SPONSOR(Nome) ON DELETE CASCADE
) ENGINE="INNODB";

CREATE TABLE UTENTE(
	Username VARCHAR(30),
    Password VARCHAR(30),
    Nome VARCHAR(25),
    Cognome VARCHAR(25),
    DataNascita DATE,
    PRIMARY KEY(Username)
) ENGINE="INNODB";

CREATE TABLE REGISTRAZIONE(
	Username VARCHAR(30),
    AcronimoConferenza VARCHAR(10),
    AnnoEdizione INT,
    PRIMARY KEY(Username, AcronimoConferenza, AnnoEdizione),
    FOREIGN KEY(AcronimoConferenza, AnnoEdizione) REFERENCES CONFERENZA(Acronimo, AnnoEdizione) ON DELETE CASCADE,
    FOREIGN KEY (Username) REFERENCES UTENTE(Username) ON DELETE CASCADE
) ENGINE="INNODB";

CREATE TABLE ADMIN(
    Username VARCHAR(30),
    PRIMARY KEY (Username),
    FOREIGN KEY (Username) REFERENCES UTENTE(Username)
) ENGINE="INNODB";

CREATE TABLE PRESENTER(
    Username VARCHAR(30),
    CurriculumVitae BLOB,
    Foto BLOB,
    NomeUniversità VARCHAR(30),
    PRIMARY KEY (Username),
    FOREIGN KEY (Username) REFERENCES UTENTE(Username)
) ENGINE="INNODB";

CREATE TABLE SPEAKER(
    Username VARCHAR(30),
    CurriculumVitae BLOB,
    Foto BLOB,
    NomeUniversità VARCHAR(30),
    PRIMARY KEY (Username),
    FOREIGN KEY (Username) REFERENCES UTENTE(Username)
) ENGINE="INNODB";

CREATE TABLE CREAZIONE(
    UsernameAdmin VARCHAR(30),
    AcronimoConferenza VARCHAR(10), 
    AnnoEdizione INT,
    PRIMARY KEY (UsernameAdmin, AcronimoConferenza, AnnoEdizione),
    FOREIGN KEY (UsernameAdmin) REFERENCES ADMIN(Username),
    FOREIGN KEY (AcronimoConferenza, AnnoEdizione) REFERENCES CONFERENZA(Acronimo, AnnoEdizione)
) ENGINE="INNODB";


# TO DO : Considerare di incorporate l'entità DATASVOLGIMENTO
# L'attributo Data di DATASVOLGIMENTO deve essere soggetto al vincolo UNIQUE
# (not null ed unico nella tabella) perché SESSIONE.Data è in vincolo di integrità
# con DATASVOLGIMENTO.Data
# Consideriamo se accorpare tutto in SESSIONE che a questo punto aggiungerebbe
# due campi ovvero AcronimoConferenza e AnnoEdizione oppure mantenere questa situazione
# (per me fragile) 
# Accorpando in SESSIONE avremmo

#################  SESSIONE SE ACCORPIAMO   ################################################
/*
CREATE TABLE SESSIONE(
    AcronimoConferenza VARCHAR(10),
    AnnoEdizione INT,
    Data DATE, 
    Codice VARCHAR(10),
    Titolo VARCHAR(30),
    Numero_Presentazioni INT,
    OraInizio DATETIME,
    OraFine DATETIME, 
    Link VARCHAR(50),
    PRIMARY KEY (Codice),
    FOREIGN KEY(AcronimoConferenza, AnnoEdizione) REFERENCES CONFERENZA(Acronimo, AnnoEdizione) ON DELETE CASCADE,
) ENGINE="INNODB";

*/

CREATE TABLE DATASVOLGIMENTO(
    AcronimoConferenza VARCHAR(10),
    AnnoEdizione INT,
    Data DATE UNIQUE,
    PRIMARY KEY (AcronimoConferenza, AnnoEdizione, Data),
    FOREIGN KEY (AcronimoConferenza, AnnoEdizione) REFERENCES CONFERENZA(Acronimo, AnnoEdizione)
) ENGINE="INNODB";

CREATE TABLE SESSIONE(
    Data DATE, 
    Codice VARCHAR(10),
    Titolo VARCHAR(30),
    Numero_Presentazioni INT,
    OraInizio DATETIME,
    OraFine DATETIME, 
    Link VARCHAR(50),
    PRIMARY KEY (Codice),
	FOREIGN KEY (Data) REFERENCES DATASVOLGIMENTO(Data)
) ENGINE="INNODB";

CREATE TABLE PRESENTAZIONEARTICOLO(
    Codice INT,
    CodiceSessione VARCHAR(10),
    OraInizio DATETIME,
    OraFine DATETIME,
    NumeroSequenza INT,
    Tipo VARCHAR(10),
    PRIMARY KEY (Codice),
    FOREIGN KEY (CodiceSessione) REFERENCES SESSIONE(Codice)
)