DROP DATABASE IF EXISTS CONFVIRTUAL;
CREATE DATABASE CONFVIRTUAL;
USE CONFVIRTUAL;

CREATE TABLE CONFERENZA(
	Nome VARCHAR(30),
    Acronimo VARCHAR(30),
	AnnoEdizione INT,
    Logo BLOB,
    Svolgimento ENUM("ATTIVA","COMPLETATA"),
    TotaleSponsorizzazioni INT,
    PRIMARY KEY(Acronimo, AnnoEdizione)
) ENGINE="INNODB";

CREATE TABLE SPONSOR(
	Nome VARCHAR(30),
    Logo BLOB,
    Importo DOUBLE,
    PRIMARY KEY(Nome)
) ENGINE="INNODB";

CREATE TABLE SPONSORIZZAZIONE(
	AcronimoConferenza VARCHAR(30),
    AnnoEdizione INT,
    NomeSponsor VARCHAR(30),
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
    Luogo VARCHAR(30),
    Tipologia ENUM("BASE","ADMIN","SPEAKER","PRESENTER") DEFAULT "BASE",
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

CREATE TABLE UNIVERSITA(
	NomeUniversità VARCHAR(30),
	NomeDipartimento VARCHAR(30),
    PRIMARY KEY(NomeUniversità)
)ENGINE="INNODB";

CREATE TABLE ADMIN(
    Username VARCHAR(30),
    PRIMARY KEY (Username),
    FOREIGN KEY (Username) REFERENCES UTENTE(Username)
) ENGINE="INNODB";

CREATE TABLE PRESENTER(
    Username VARCHAR(30),
    CurriculumVitae VARCHAR(30),
    Foto BLOB,
    NomeUniversità VARCHAR(30),
    PRIMARY KEY (Username),
    FOREIGN KEY (Username) REFERENCES UTENTE(Username),
    FOREIGN KEY(NomeUniversità) REFERENCES UNIVERSITA(NomeUniversità)
) ENGINE="INNODB";

CREATE TABLE SPEAKER(
    Username VARCHAR(30),
    CurriculumVitae VARCHAR(30),
    Foto BLOB,
    NomeUniversità VARCHAR(30),
    PRIMARY KEY (Username),
    FOREIGN KEY (Username) REFERENCES UTENTE(Username),
	FOREIGN KEY(NomeUniversità) REFERENCES UNIVERSITA(NomeUniversità)
) ENGINE="INNODB";

CREATE TABLE CREAZIONE(
    UsernameAdmin VARCHAR(30),
    AcronimoConferenza VARCHAR(10), 
    AnnoEdizione INT,
    PRIMARY KEY (UsernameAdmin, AcronimoConferenza, AnnoEdizione),
    FOREIGN KEY (UsernameAdmin) REFERENCES ADMIN(Username),
    FOREIGN KEY (AcronimoConferenza, AnnoEdizione) REFERENCES CONFERENZA(Acronimo, AnnoEdizione)
) ENGINE="INNODB";

CREATE TABLE DATASVOLGIMENTO(
    AcronimoConferenza VARCHAR(30),
    AnnoEdizione INT,
    Data DATE,
    PRIMARY KEY (AcronimoConferenza, AnnoEdizione, Data),
    FOREIGN KEY (AcronimoConferenza, AnnoEdizione) REFERENCES CONFERENZA(Acronimo, AnnoEdizione) ON DELETE CASCADE
) ENGINE="INNODB";

CREATE TABLE SESSIONE(
    AcronimoConferenza VARCHAR(30),
    Anno INT,
    Data DATE,
    Codice INT AUTO_INCREMENT,
    Titolo VARCHAR(30),
    Numero_Presentazioni INT,
    OraInizio DATETIME,
    OraFine DATETIME, 
    Link VARCHAR(50),
    PRIMARY KEY (Codice),
	FOREIGN KEY (AcronimoConferenza, Anno, Data) REFERENCES DATASVOLGIMENTO(AcronimoConferenza, AnnoEdizione, Data) ON DELETE CASCADE
) ENGINE="INNODB";

CREATE TABLE PRESENTAZIONE(
    Codice INT,
	CodiceSessione INT,
    OraInizio DATETIME,
    OraFine DATETIME,
    NumeroSequenza INT,
    Tipologia ENUM("ARTICOLO", "TUTORIAL"),
    PRIMARY KEY (Codice),
    FOREIGN KEY (CodiceSessione) REFERENCES SESSIONE(Codice) ON DELETE CASCADE
)ENGINE="INNODB";

CREATE TABLE P_ARTICOLO(
	CodicePresentazione INT,
    Titolo VARCHAR(30),
    NumeroPagine INT,
    FilePDF BLOB,
    StatoSvolgimento ENUM ("COPERTO", "NON COPERTO"),
    UsernamePresenter VARCHAR(30),
    PRIMARY KEY (CodicePresentazione),
    FOREIGN KEY(CodicePresentazione) REFERENCES PRESENTAZIONE(Codice) ON DELETE CASCADE,
    FOREIGN KEY(UsernamePresenter) REFERENCES PRESENTER(Username)
)ENGINE="INNODB";

CREATE TABLE P_TUTORIAL(
	CodicePresentazione INT,
	Titolo VARCHAR(30),
	Abstract VARCHAR(500),
	PRIMARY KEY (CodicePresentazione),
	FOREIGN KEY(CodicePresentazione) REFERENCES PRESENTAZIONE(Codice)
)ENGINE="INNODB";

CREATE TABLE AUTORE(
	Nome VARCHAR(25),
	Cognome VARCHAR(25),
	CodiceArticolo INT,
	PRIMARY KEY(Nome, Cognome, CodiceArticolo),
	FOREIGN KEY(CodiceArticolo) REFERENCES P_ARTICOLO(CodicePresentazione)
)ENGINE="INNODB";

CREATE TABLE KEYWORD(
	Parola VARCHAR(20),
	CodiceArticolo INT,
	PRIMARY KEY(Parola, CodiceArticolo),
	FOREIGN KEY (CodiceArticolo) REFERENCES P_ARTICOLO(CodicePresentazione)
)ENGINE="INNODB";

CREATE TABLE VALUTAZIONE(
	UsernameAdmin VARCHAR(30),
	Voto INT,
	Note VARCHAR(50),
	CodicePresentazione INT,
	PRIMARY KEY(UsernameAdmin, CodicePresentazione),
	FOREIGN KEY(UsernameAdmin) REFERENCES ADMIN(Username),
	FOREIGN KEY(CodicePresentazione) REFERENCES PRESENTAZIONE(Codice)
)ENGINE="INNODB";

CREATE TABLE RISORSA(
	UsernameProprietario VARCHAR(30),
    Link VARCHAR(50),
    Descrizione VARCHAR(50),
    CodiceTutorial INT,
    PRIMARY KEY(UsernameProprietario),
    FOREIGN KEY(UsernameProprietario) REFERENCES SPEAKER(Username),
    FOREIGN KEY(CodiceTutorial) REFERENCES P_TUTORIAL(CodicePresentazione)
)ENGINE="INNODB";

CREATE TABLE SPEAKER_TUTORIAL(
	UsernameSpeaker VARCHAR(30),
	CodiceTutorial INT,
	PRIMARY KEY(UsernameSpeaker, CodiceTutorial),
	FOREIGN KEY(UsernameSpeaker) REFERENCES SPEAKER(Username),
	FOREIGN KEY(CodiceTutorial) REFERENCES P_TUTORIAL(CodicePresentazione) 
)ENGINE="INNODB";

CREATE TABLE FAVORITE(
	Username VARCHAR(30),
	CodicePresentazione INT, 
	PRIMARY KEY(Username,CodicePresentazione),
	FOREIGN KEY(Username)REFERENCES UTENTE(Username),
	FOREIGN KEY(CodicePresentazione)REFERENCES PRESENTAZIONE(Codice)
)ENGINE="INNODB";

CREATE TABLE MESSAGGIO(
	UsernameMittente VARCHAR(30),
	Testo VARCHAR(200),
	DataInserimento DATE,
	ChatID INT,
	PRIMARY KEY(UsernameMittente,Testo,DataInserimento,ChatID),
	FOREIGN KEY(UsernameMittente) REFERENCES UTENTE(Username),
	FOREIGN KEY(ChatID) REFERENCES SESSIONE(Codice)
)ENGINE="INNODB";

###########################
##### STORED PROCEDURES ###
###########################

# Iserisce un nuovo utente
DELIMITER $
CREATE PROCEDURE InserisciUtente(IN Username VARCHAR(30), IN Password VARCHAR(30), IN Nome VARCHAR(25), IN Cognome VARCHAR(25), IN DataNascita DATE, IN Luogo VARCHAR(30)) 
BEGIN
	START TRANSACTION;
		INSERT INTO UTENTE(Username, Password, Nome, Cognome, DataNascita, Luogo) VALUES(Username, Password, Nome, Cognome, DataNascita, Luogo);
		IF(EXISTS(SELECT * FROM UTENTE WHERE UTENTE.Username = Username)) THEN
			SELECT CONCAT("INSERT SUCCESFULL") AS MESSAGE;
		ELSE 
			SELECT CONCAT("A PROBLEM OCCURED") AS MESSAGE;
		END IF;
    COMMIT WORK;
END;
$ DELIITER ;

# Creazione di una nuova conferenza
DELIMITER $
CREATE PROCEDURE CreaConferenza(IN Nome VARCHAR(30), IN Acronimo VARCHAR(30), IN AnnoEdizione INT, IN Svolgimento ENUM("ATTIVA","COMPLETATA")) 
BEGIN
	START TRANSACTION;
		INSERT INTO CONFERENZA(Nome, Acronimo, AnnoEdizione, Svolgimento) VALUES(Nome, Acronimo, AnnoEdizione, Svolgimento);
    COMMIT WORK;
END;
$ DELIITER ;

# Creazione di una conferenza da parte di un admin, che viene registrato ad essa automaticamente
DELIMITER $
CREATE PROCEDURE CreaConferenzaAdmin(IN Nome VARCHAR(30), IN UsernameAdmin VARCHAR(30), IN Acronimo VARCHAR(30), IN AnnoEdizione INT, IN Svolgimento ENUM("ATTIVA","COMPLETATA")) 
BEGIN
	START TRANSACTION;
		IF(EXISTS(SELECT * FROM ADMIN WHERE ADMIN.Username = UsernameAdmin)) THEN
			INSERT INTO CONFERENZA(Acronimo, AnnoEdizione, Svolgimento) VALUES(Acronimo, AnnoEdizione, Svolgimento);
			INSERT INTO REGISTRAZIONE(UsernameAdmin, Acronimo, AnnoEdizione) VALUES(Username, Acronimo, Anno);
		END IF;
    COMMIT WORK;
END;
$ DELIITER ;

# Aggiunge una nuova sponsorizzazione
DELIMITER $
CREATE PROCEDURE AggiungiSponsorizzazione(IN NomeSponsor VARCHAR(30), IN AcronimoConferenza VARCHAR(30), IN Importo DOUBLE, IN Anno INT)
BEGIN
	IF(NOT EXISTS(SELECT * FROM SPONSOR AS S WHERE S.Nome = NomeSponsor)) THEN
		INSERT INTO SPONSOR(Nome, Importo) VALUES(NomeSponsor, Importo);
		INSERT INTO SPONSORIZZAZIONE(NomeSponsor, AnnoEdizione, AcronimoConferenza) VALUES(NomeSponsor, Anno, AcronimoConferenza);
	ELSE 
		INSERT INTO SPONSORIZZAZIONE(NomeSponsor, AnnoEdizione, AcronimoConferenza) VALUES(NomeSponsor, Anno, AcronimoConferenza);
    END IF;
END;
$ DELIMITER ;

# Inserisce le date di sovlgimento di una conferenza
DELIMITER $ 
CREATE PROCEDURE InserisciDateSvoglimento(IN AcronimoConferenza VARCHAR(30), IN Anno INT, IN DataInizio DATE, IN DataFine DATE)
BEGIN
	START TRANSACTION;
		BEGIN
			
            DECLARE i INT;
            DECLARE currentDate DATE;
            
            SET i = DataFine - DataInizio;
            SET currentDate = DataInizio;
           
			loop_label:  LOOP
				IF  i < 0 THEN 
					LEAVE  loop_label;
				END  IF;
					
				INSERT INTO DATASVOLGIMENTO(AcronimoConferenza, AnnoEdizione, Data) VALUES(AcronimoConferenza, Anno, currentDate);
				SET  i = i - 1;
				SET currentDate = DATE_ADD(currentDate, INTERVAL 1 DAY);
			
            END LOOP;
		END;
    COMMIT WORK;
END;
$ DELIMITER ;

DELIMITER $
CREATE PROCEDURE CreaSessione(IN AcronimoConferenza VARCHAR(30), IN Anno INT ,IN Data DATE, IN OraInizio DATETIME, IN OraFine DATETIME, IN Link VARCHAR(50))
BEGIN
	START TRANSACTION;
		
        IF(EXISTS(SELECT * FROM DATASVOLGIMENTO AS D WHERE D.AcronimoConferenza = AcronimoConferenza AND D.Data = Data)) THEN
			SELECT @acronimo:=C.Acronimo, @anno:=C.AnnoEdizione
			FROM CONFERENZA AS C, DATASVOLGIMENTO AS D
			WHERE D.AcronimoConferenza = C.Acronimo AND D.AnnoEdizione = C.AnnoEdizione AND C.Acronimo = AcronimoConferenza AND Data = D.Data;
			INSERT INTO SESSIONE(AcronimoConferenza, Data, Anno, OraInizio, OraFine, Link) VALUES(@acronimo, Data, @anno, OraInizio, OraFine, Link);
        ELSE 
			SELECT CONCAT("THE CONFERENCE YOU SELECTED IS NOT SCHEDULED ON THIS DAY") AS MESSAGE;
        END IF;
    COMMIT WORK;
END;
$ DELIMITER ;

# Registra un utente ad una conferenza
DELIMITER $
CREATE PROCEDURE RegistrazioneConferenza(IN Username VARCHAR(30), IN Acronimo VARCHAR(30), IN Anno INT)
BEGIN
	START TRANSACTION;
		IF(EXISTS(SELECT * FROM UTENTE AS U WHERE U.Username = Username) AND EXISTS(SELECT * FROM CONFERENZA AS C WHERE C.Acronimo = Acronimo AND C.AnnoEdizione = Anno)) THEN
			INSERT INTO REGISTRAZIONE(Username, AcronimoConferenza, AnnoEdizione) VALUES(Username, Acronimo, Anno);
		ELSE 
			SELECT CONCAT("INPUT DATA INCORRECT") AS MESSAGE;
        END IF;
    COMMIT WORK;
END;
$ DELIMITER ;

# Cambia il ruolo di un Utente esistente
DELIMITER $
CREATE PROCEDURE CambiaRuolo(IN username VARCHAR(30), IN newRole ENUM("BASE","ADMIN","SPEAKER","PRESENTER"))
BEGIN
	START TRANSACTION;
		IF(EXISTS(SELECT * FROM UTENTE WHERE UTENTE.Username = username)) THEN
			UPDATE UTENTE SET UTENTE.Tipologia = newRole WHERE(Utente.Username = username);
			SELECT CONCAT("OPERATION SUCESSFULL") AS MESSAGE;
		ELSE 
			SELECT CONCAT("A PROBLEM OCCURED") AS MESSAGE;
		END IF;
    COMMIT WORK;
END;
$ DELIMITER ;

######## TRIGGER #########

# TRIGGER
# Utilizzare un	trigger per	 implementare	 l’operazione	 cambio	 di	 stato_svolgimento di	
# una	 presentazione	 di articolo,	 portandolo	 da	 “Non coperto”  a “Coperto” quando si	
# inserisce	un presenter	valido	per	quella	presentazione
DELIMITER $
CREATE TRIGGER CambiaStatoPresentazione
AFTER UPDATE ON P_ARTICOLO
FOR EACH ROW 
BEGIN
	UPDATE P_ARTICOLO SET StatoSvolgimento = "COPERTO" WHERE NEW.UsernamePresenter = P_ARTICOLO.UsernamePresenter;
END;
$ DELIMITER ;

# Utilizzare un	 trigger per	 implementare	 l’operazione	 di	 aggiornamento	 del	 campo	
# numero_presentazioni ogni	 qualvolta	 si	 aggiunge	 una	 nuova	 presentazione ad	 una	
# sessione	della	conferenza
DELIMITER $
CREATE TRIGGER AggiungiPresentazione
AFTER INSERT ON PRESENTAZIONE
FOR EACH ROW
BEGIN
	UPDATE SESSIONE SET SESSIONE.NumeroPresentazione = SESSIONE.NumeroPresentazione + 1 WHERE SESSIONE.Codice = NEW.CodiceSessione;
END;
$ DELIMITER ;

# Un presenter deve essere necessariamente un autore dell'articolo
DELIMITER $
CREATE TRIGGER CheckPresenter
BEFORE INSERT ON P_ARTICOLO
FOR EACH ROW
BEGIN
	IF( NOT EXISTS(SELECT * FROM AUTORE AS A WHERE A.Nome IN (SELECT U.Nome FROM UTENTE AS U WHERE U.Username = NEW.UsernamePresenter))) THEN
		SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = "PRESENTER IS NOT THE AUTHOR";
    END IF;
END;
DELIMITER $ ; 

DELIMITER $
CREATE TRIGGER CheckOrariPresentazioni
BEFORE INSERT ON PRESENTAZIONE
FOR EACH ROW
BEGIN
	IF(NEW.OraInizio < ANY (SELECT S.OraInizio FROM SESSIONE AS S WHERE S.Codice = NEW.CodiceSessione) OR (NEW.OraFine > ANY (SELECT S.OraInizio FROM SESSIONE AS S WHERE S.Codice = NEW.CodiceSessione))) THEN
		SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = "DATETIME ERROR";
    END IF;
END;
$ DELIMITER ;

DELIMITER $
CREATE TRIGGER AggiornaSponsor
AFTER INSERT ON SPONSORIZZAZIONE
FOR EACH ROW
BEGIN
	UPDATE CONFERENZA SET TotaleSponsorizzazioni = TotaleSponsorizzazioni + 1 WHERE CONFERENZA.Acronimo = NEW.AcronimoConferenza;
END;
$ DELIMITER ;










 
