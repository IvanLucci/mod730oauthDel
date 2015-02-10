/*
Tabelle per il database del AS dedicate alle deleghe.
*/

-- Utente delega un altro utente per un insieme di scope, fino a una certa data di scadenza.
-- drop table if exists `delegations`;
create table `delegations`(
	`delegator` varchar(500) not null,
	`delegate` varchar(500) not null,
	`scopes` varchar(1000) not null,
	`expiration_date` date,
	primary key(`delegator`, `delegate`)
)ENGINE=InnoDB DEFAULT CHARSET=latin1;


/*
Tabelle per il database del AS dedicate ai codici di verifica dell'attivazione delega.
*/

create table `delegation_verification_code`(
	`delegator` varchar(500) not null,
	`delegate` varchar(500) not null,
	`scopes` varchar(1000) not null,
	`expiration_date` date,
	`code` bigint not null,
	primary key(`delegator`, `delegate`)
)ENGINE=InnoDB DEFAULT CHARSET=latin1;


/*
Tabelle per i ruoli 
*/

--Lista di ruoli
--drop table if exists `roles`;
create table `roles`(
	`role_id` smallint not null auto_increment,
	`role_name` varchar(100) not null,
	`role_uri` varchar(200) not null,
	primary key(`role_id`)	
)ENGINE=InnoDB DEFAULT CHARSET=latin1;


--Ruoli e relativi scope permessi
--drop table if exists `role_scopes`;
create table `role_scopes`(
	`role_id` smallint not null references `roles`(`role_id`) on delete cascade on update cascade,
	`scopes` varchar(1000),
	primary key(`role_id`)
)ENGINE=InnoDB DEFAULT CHARSET=latin1;




--Dati
insert into delegations (`delegator`, `delegate`, `scopes`, `expiration_date`) values
('mario.rossi@gmail.com', 'bianchi@gmail.com', 'lettura_dati_anagrafici', null);

insert into `roles` (`role_name`,`role_uri`) values
('ispettore','https://localhost/roleServer/public/index');

insert into `role_scopes` (`role_id`, `scopes`) values
(1, 'lettura_dati_contratti_locazione_fabbricati lettura_dati_cud');
