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





