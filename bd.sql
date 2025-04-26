create table Brocanteur(
    bid int primary key auto_increment,
    nom varchar(255) not null,
    prenom varchar(255) not null,
    courriel varchar(255) not null unique, -- UNIQUE
    mot_passe varchar(255) not null,
    photo varchar(255),
    description varchar(1024) not null,
    visible boolean not null,
    est_administrateur boolean not null,
    zid int not null,
    foreign key (zid) references Zone(zid)
);

create table Zone(
    zid int primary key auto_increment,
    nom varchar(50) not null,
    description varchar(1024) not null
);

create table Emplacement(
    eid int primary key auto_increment,
    code int unique, -- UNIQUE
    foreign key (code) references Zone(zid)
);

-- Object
create table Objet(
    oid int primary key auto_increment,
    intitule varchar(50) not null,
    prix int not null,
    description varchar(1024) not null,
    image varchar(255),
    bid int not null,
    foreign key (bid) references Brocanteur(bid)
);

-- Categorie
create table Categorie(
    cid int primary key auto_increment,
    intitule varchar(50) not null
);