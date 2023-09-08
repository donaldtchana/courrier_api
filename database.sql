drop database if exists MCS_DB;
create database MCS_DB;
use MCS_DB;

create table Personne(
	id int unsigned auto_increment,
	nom varchar(128) not null,
    email varchar(255) not null unique,
    password varchar(255) not null,
    primary key(id)
);

create table Utilisateur(
	userId int unsigned auto_increment,
    personneId int unsigned not null unique, 
    primary key(userId),
    foreign key(personneId) references Personne(id) on delete cascade on update cascade
);

create table Admin(
	adminId int auto_increment,
    personneId int unsigned not null unique,
    primary key(adminId),
    foreign key(personneId) references Personne(id) on delete cascade on update cascade
);

create table Produits(
	productId int unsigned auto_increment,
	nom varchar(20) not null unique,
    prix smallint unsigned not null default 0,
    quantite smallint unsigned default 0,
    description varchar(70) default '',
    primary key(productId)
);

create table Service(
	serviceId int unsigned auto_increment,
    nom varchar(20),
    primary key(serviceId)
);

create table Commandes(
	orderNumber int unsigned auto_increment,
    userId int unsigned not null,
    orderDateTime datetime default current_timestamp,
    primary key(orderNumber),
    foreign key(userId) references Utilisateur(userId) on update cascade
);

create index prix_idx on Produits(prix);
create table Details(
	orderNumber int unsigned,
    productId int unsigned not null,
    quantity smallint unsigned,
    price smallint unsigned,
    primary key(orderNumber, productId),
    foreign key(orderNumber) references Commandes(orderNumber) on update cascade on delete cascade,
    foreign key(productId) references Produits(productId) on update cascade on delete cascade,
    foreign key(price) references Produits(prix) on update cascade
);



-- Random Data

-- Personne table
insert into Personne (nom, email, password) values
('John Doe', 'johndoe@example.com', 'password'),
('Jane Doe', 'janedoe@example.com', 'password'),
('Mary Smith', 'marysmith@example.com', 'password');

-- Utilisateur table
insert into Utilisateur (personneId) values
(1),
(2),
(3);

-- Admin table
insert into Admin (personneId) values
(1);

-- Produits table
insert into Produits (nom, prix, quantite, description) values
('iPhone 13', 1000, 100, 'The latest and greatest iPhone'),
('MacBook Pro', 1500, 50, 'The most powerful MacBook'),
('AirPods Pro', 250, 200, 'The best wireless earbuds'),
('TV', 350, 100, '42" wide smart screen');

-- Service table
insert into Service (nom) values
('Delivery'),
('Installation'),
('Repair');

-- Commandes table
insert into Commandes (userId, orderDateTime) values
(1, '2023-07-29'),
(2, '2023-07-30'),
(3, '2023-07-31');

-- Details table
insert into Details (orderNumber, productId, quantity, price) values
(1, 1, 1, 1000),
(1, 2, 1, 1500),
(2, 3, 2, 250);

