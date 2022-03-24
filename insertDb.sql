USE CONFVIRTUAL;
call confvirtual.InserisciUtente('mic', 'micki', 'Michele', 'Dinelli', '2000-07-27', 'Bologna');
call confvirtual.InserisciUtente('benze', 'bra', 'Filippo', 'Brajucha', '2000-11-10', 'Treviso');
call confvirtual.InserisciUtente('pino', 'accio', 'Youssef', 'Hanna', '2000-02-21', 'VDA');
call confvirtual.InserisciUtente('fede', 'bella', 'Federico', 'Bellati', '2000-01-11', 'VDA');
call confvirtual.InserisciUtente('bepo', 'fachine', 'Gabriel', 'Pitti', '2000-09-29', 'VDA');

call confvirtual.CambiaRuolo('mic', 'PRESENTER');
call confvirtual.CambiaRuolo('benze', 'ADMIN');
call confvirtual.CambiaRuolo('pino', 'SPEAKER');
call confvirtual.CambiaRuolo('fede', 'SPEAKER');
call confvirtual.CambiaRuolo('bepo', 'PRESENTER');

call confvirtual.CreaConferenzaAdmin('IEEEConference', 'benze', 'IEEE', 2022);
call confvirtual.CreaConferenzaAdmin('MLConference', 'benze', 'MLC', 2022);
call confvirtual.CreaConferenzaAdmin('AI4Health', 'benze', 'AI4H', 2022);

call confvirtual.InserisciDateSvoglimento('IEEE', 2022, '2022-01-01', '2022-01-02');
call confvirtual.InserisciDateSvoglimento('MLC', 2022, '2022-01-10', '2022-01-15');
call confvirtual.InserisciDateSvoglimento('AI4H', 2022, '2022-05-3', '2022-05-4');

call confvirtual.CreaSessione('IEEE', 'IEEE1', 2022, '2022-01-01', '10:00:00', '13:00:00', 'www.google.com');
call confvirtual.CreaSessione('IEEE', 'IEEE2', 2022, '2022-01-01', '11:00:00', '14:00:00', 'www.teams.com');
call confvirtual.CreaSessione('AI4H', 'AI4H1', 2022, '2022-05-3', '17:00:00', '18:00:00', 'www.teams.com');
call confvirtual.CreaSessione('AI4H', 'AI4H2', 2022, '2022-05-4', '15:00:00', '15:30:00', 'www.teams.com');

call confvirtual.InserisciArticolo(1, '11:00:00', '13:00:00', 'Le pinacce', 100, '');
call confvirtual.InserisciArticolo(2, '11:30:00', '13:00:00', 'Le pinacce Pt2', 100, '');
call confvirtual.InserisciTutorial(2, '12:00:00', '13:40:00', 'Michele in viaggio', 'mic era in viaggio blah blah blah');
call confvirtual.InserisciTutorial(1, '12:00:00', '12:45:00', 'Pino insegna JS', 'Pino insegna Js');
call confvirtual.InserisciTutorial(3, '17:30:00', '17:40:00', 'Pino insegna CSS', 'Pino insegna CSS');

INSERT INTO AUTORE(Nome, Cognome) VALUES ("Michele", "Dinelli");
INSERT INTO AUTORE(Nome, Cognome) VALUES ("Filippo", "Brajucha");
INSERT INTO AUTORE(Nome, Cognome) VALUES ("Youssef", "Hanna");
INSERT INTO AUTORE(Nome, Cognome) VALUES("Federico", "Bellati");

INSERT INTO SCRITTURA(NomeAutore, CognomeAutore, CodiceArticolo) VALUES("Michele", "Dinelli", 1);
INSERT INTO SCRITTURA(NomeAutore, CognomeAutore, CodiceArticolo) VALUES("Federico", "Bellati", 1);
INSERT INTO SCRITTURA(NomeAutore, CognomeAutore, CodiceArticolo) VALUES("Filippo", "Brajucha", 1);
INSERT INTO SCRITTURA(NomeAutore, CognomeAutore, CodiceArticolo) VALUES("Youssef", "Hanna", 2);

call confvirtual.AggiungiSponsorizzazione('brajucha', 'AI4H', 1500, 2022);
call confvirtual.AggiungiSponsorizzazione('brajucha', 'MLC', 300, 2022);
call confvirtual.AggiungiSponsorizzazione('dinelli', 'IEEE', 700, 2022);
call confvirtual.AggiungiSponsorizzazione('pinazza', 'MLC', 27000, 2022);

call confvirtual.InserisiciMessaggio('mic', 'ciao', 1, '2022-07-25 12:20:00');
call confvirtual.InserisiciMessaggio('mic', 'come va?', 1, '2022-07-25 12:20:30');
call confvirtual.InserisiciMessaggio('mic', 'bene tu?', 1, '2022-07-25 12:21:00');
call confvirtual.InserisiciMessaggio('pino', 'si eh', 1, '2022-07-25 12:22:00');
call confvirtual.InserisiciMessaggio('mic', 'ciao pino', 2, '2022-07-25 11:17:00');
call confvirtual.InserisiciMessaggio('pino', 'ciao mic viva la pucchiacchia', 2, '2022-07-25 11:21:00');
call confvirtual.InserisiciMessaggio('mic', 'si è vero', 2, '2022-07-25 11:22:00');
call confvirtual.InserisiciMessaggio('pino', 'a me piace naruto', 2, '2022-07-25 11:23:00');
call confvirtual.InserisiciMessaggio('mic', 'anche a me', 2, '2022-07-25 11:31:00');
call confvirtual.InserisiciMessaggio('pino', 'viva saske', 2, '2022-07-25 11:47:00');

INSERT INTO UNIVERSITA(NomeUniversità, NomeDipartimento) VALUES("Unibo", "Informatica");
INSERT INTO UNIVERSITA(NomeUniversità, NomeDipartimento) VALUES("Unimi", "Lingue");
INSERT INTO UNIVERSITA(NomeUniversità, NomeDipartimento) VALUES("Unitn", "statistica");
INSERT INTO UNIVERSITA(NomeUniversità, NomeDipartimento) VALUES("Unibo", "Ingegneria");

INSERT INTO FAVORITE(Username, CodicePresentazione) VALUES("mic", "1");
INSERT INTO FAVORITE(Username, CodicePresentazione) VALUES("mic", "3");
/*INSERT INTO AUTORE(Nome, Cognome) VALUES("Youssef", "Hanna");
INSERT INTO SCRITTURA(NomeAutore, CognomeAutore, CodiceArticolo) VALUES("Youssef", "Hanna", 1);
*/

#call confvirtual.AssociaPresenter('mic', 1);
#call confvirtual.AssociaPresenter('mic', 2);
call confvirtual.AssociaSpeaker('pino', 3);
call confvirtual.AssociaSpeaker('fede', 4);

call confvirtual.InserisciValutazione('benze',10,'Molto bello, a dir poco fantastico', 2);

INSERT INTO VALUTAZIONE(UsernameAdmin, Voto, CodicePresentazione) VALUES("benze",10,1);
#INSERT INTO VALUTAZIONE(UsernameAdmin, Voto, CodicePresentazione) VALUES("benze",7,2);
INSERT INTO VALUTAZIONE(UsernameAdmin, Voto, CodicePresentazione) VALUES("benze", 5, 3);
INSERT INTO VALUTAZIONE(UsernameAdmin, Voto, CodicePresentazione) VALUES("benze", 9, 4);
