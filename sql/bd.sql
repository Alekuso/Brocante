-- tables
create table Zone(
    zid int primary key auto_increment,
    nom varchar(50) not null,
    description varchar(1024) not null
);

create table Brocanteur(
    bid int primary key auto_increment,
    nom varchar(255) not null,
    prenom varchar(255) not null,
    courriel varchar(255) not null unique, -- UNIQUE
    mot_passe varchar(255) not null,
    photo varchar(255),
    description varchar(1024) not null,
    visible boolean not null,
    est_administrateur boolean not null
);

create table Emplacement(
    eid int primary key auto_increment,
    code varchar(10) unique,
    zid int not null,
    bid int unique,
    foreign key (zid) references Zone(zid),
    foreign key (bid) references Brocanteur(bid)
);

create table Categorie(
    cid int primary key auto_increment,
    intitule varchar(50) not null
);

create table Objet(
    oid int primary key auto_increment,
    intitule varchar(50) not null,
    prix decimal(10,2) not null,
    description varchar(1024) not null,
    image varchar(255),
    bid int not null,
    cid int,
    foreign key (bid) references Brocanteur(bid),
    foreign key (cid) references Categorie(cid)
);

-- Sample data
INSERT INTO Zone (zid, nom, description) VALUES 
(1, 'Zone A', 'La plus grande rue de la brocante'),
(2, 'Zone B', 'Petite Adjacente au mi-A'),
(3, 'Zone C', 'Adjacente à la fin de A');

INSERT INTO Categorie (intitule) VALUES 
('Consoles'),
('Jeux Vidéo'),
('Accessoires'),
('Ordinateurs Rétro'),
('Magazines Gaming'),
('Figurines'),
('Bornes d''arcade'),
('Goodies');

INSERT INTO Brocanteur (nom, prenom, courriel, mot_passe, description, visible, est_administrateur) VALUES 
('Dupont', 'Jean', 'jean.dupont@gmail.com', '$2y$10$kCrAl/e.lS0zqDOzvdRQMOg6TU4kHLukxQwSv0zHWtVGT0AoXSCeK', 'Collectionneur de consoles Nintendo depuis 1990', 1, 0),
('Martin', 'Sophie', 'sophie.martin@hotmail.com', '$2y$10$kCrAl/e.lS0zqDOzvdRQMOg6TU4kHLukxQwSv0zHWtVGT0AoXSCeK', 'Passionnée de jeux PS1 et PS2', 1, 0),
('Petit', 'Michel', 'michel.petit@outlook.be', '$2y$10$kCrAl/e.lS0zqDOzvdRQMOg6TU4kHLukxQwSv0zHWtVGT0AoXSCeK', 'Spécialiste en jeux rétro SEGA', 1, 0),
('Leroy', 'Julie', 'julie.leroy@gmail.com', '$2y$10$kCrAl/e.lS0zqDOzvdRQMOg6TU4kHLukxQwSv0zHWtVGT0AoXSCeK', 'Fan de RPG japonais et collectionneuse de figurines', 1, 0),
('Bernard', 'Thomas', 'thomas.bernard@outlook.be', '$2y$10$kCrAl/e.lS0zqDOzvdRQMOg6TU4kHLukxQwSv0zHWtVGT0AoXSCeK', 'Collectionneur d''ordinateurs vintage et jeux DOS', 1, 0),
('Lambert', 'Marie', 'marie.lambert@hotmail.com', '$2y$10$kCrAl/e.lS0zqDOzvdRQMOg6TU4kHLukxQwSv0zHWtVGT0AoXSCeK', 'Achète et vend des jeux Game Boy et Game Boy Color', 1, 0),
('Durand', 'Alexandre', 'alexandre.durand@gmail.com', '$2y$10$kCrAl/e.lS0zqDOzvdRQMOg6TU4kHLukxQwSv0zHWtVGT0AoXSCeK', 'Amateur de FPS et jeux d''action des années 2000', 1, 0),
('Lefèvre', 'Nathalie', 'nathalie.lefevre@outlook.be', '$2y$10$kCrAl/e.lS0zqDOzvdRQMOg6TU4kHLukxQwSv0zHWtVGT0AoXSCeK', 'Vente de magazines et guides stratégiques rétro', 1, 0),
('Dubois', 'Patrick', 'patrick.dubois@hotmail.com', '$2y$10$kCrAl/e.lS0zqDOzvdRQMOg6TU4kHLukxQwSv0zHWtVGT0AoXSCeK', 'Collectionneur de consoles Xbox et jeux PC', 1, 0),
('Moreau', 'Isabelle', 'isabelle.moreau@gmail.com', '$2y$10$kCrAl/e.lS0zqDOzvdRQMOg6TU4kHLukxQwSv0zHWtVGT0AoXSCeK', 'Spécialiste en éditions limitées et collectors', 1, 0),
('Olemans', 'Alex', 'alex@femboy.be', '$2y$10$kCrAl/e.lS0zqDOzvdRQMOg6TU4kHLukxQwSv0zHWtVGT0AoXSCeK', 'Administrateur du site', 0, 1),
('Admin', 'Admin', 'a.olemans@student.helmo.be', '$2y$10$kCrAl/e.lS0zqDOzvdRQMOg6TU4kHLukxQwSv0zHWtVGT0AoXSCeK', 'Administrateur du site', 0, 1);

INSERT INTO Emplacement (code, zid, bid) VALUES 
('A1', 1, 1),
('A2', 1, 4),
('A3', 1, 7),
('A4', 1, 10),
('B1', 2, 2),
('B2', 2, 5),
('C1', 3, 3),
('C2', 3, 6);

INSERT INTO Objet (intitule, prix, description, image, bid, cid) VALUES 
('Nintendo NES', 45.00, 'Console Nintendo NES en bon état avec manette d''origine', 'nes.jpg', 1, 1),
('Super Mario Bros 3', 15.00, 'Cartouche Super Mario Bros 3 pour NES, testée et fonctionnelle', 'mario3.jpg', 1, 2),
('PlayStation 1', 35.00, 'Console PlayStation 1 avec deux manettes et memory card', 'ps1.jpg', 2, 1),
('Final Fantasy VII', 20.00, 'Final Fantasy VII pour PS1, version PAL, CD en bon état', 'ff7.jpg', 2, 2),
('SEGA Mega Drive', 40.00, 'SEGA Mega Drive modèle 1 avec câbles et une manette', 'megadrive.jpg', 3, 1),
('Sonic The Hedgehog 2', 8.00, 'Jeu Sonic 2 pour Mega Drive, cartouche seule', 'sonic2.jpg', 3, 2),
('Game Boy Color', 25.00, 'Game Boy Color violette translucide, quelques rayures', 'gbc.jpg', 6, 1),
('Pokémon Version Jaune', 18.00, 'Pokémon Jaune pour Game Boy, pile à changer', 'pokemonjaune.jpg', 6, 2),
('PlayStation 2', 30.00, 'PS2 Slim noire avec manette et memory card 8MB', 'ps2.jpg', 2, 1),
('Devil May Cry 3', 8.00, 'Devil May Cry 3 pour PS2, boîtier abîmé', 'dmc3.jpg', 2, 2),
('XBOX 360', 45.00, 'XBOX 360 Elite avec manette sans fil', 'xbox360.jpg', 9, 1),
('Halo 3', 5.00, 'Halo 3 pour Xbox 360, édition classique', 'halo3.jpg', 9, 2),
('Manette NES', 8.00, 'Manette NES d''origine, boutons un peu durs', 'manetteNES.jpg', 1, 3),
('Game Genie NES', 15.00, 'Game Genie pour NES, sans livret', 'gamegenie.jpg', 1, 3),
('Macintosh Classic', 120.00, 'Apple Macintosh Classic de 1990, à réparer', 'macclassic.jpg', 5, 4),
('Commodore 64', 65.00, 'Commodore 64 avec lecteur de disquettes à réviser', 'c64.jpg', 5, 4),
('Guide Zelda OoT', 8.00, 'Guide officiel Zelda Ocarina of Time, pages jaunies', 'guidezelda.jpg', 8, 5),
('Bartop Arcade', 150.00, 'Borne d''arcade bartop à réparer, avec écran LCD', 'bartop.jpg', 10, 7),
('Neo Geo MVS', 180.00, 'Borne Neo Geo MVS 2 slots, à restaurer', 'neogeo.jpg', 10, 7),
('T-shirt Link Faces of Evil', 5.00, 'T-shirt du meilleur jeu qui est Link The Faces of Evil. taille XXL, délavé et très adoré', 'tshirtlink.jpg', 10, 8),
('Mug Pac-Man', 3.00, 'Mug Pac-Man, légèrement ébréché', 'mugpacman.jpg', 7, 8),
('Nintendo 64', 45.00, 'Console N64 avec câbles, sans manette', 'n64.jpg', 1, 1),
('Mario Kart 64', 15.00, 'Jeu Mario Kart 64, cartouche seule', 'mariokart64.jpg', 1, 2),
('GameCube', 35.00, 'Nintendo GameCube noire avec câbles, sans manette', 'gamecube.jpg', 7, 1),
('Zelda Wind Waker', 20.00, 'The Legend of Zelda: Wind Waker, CD rayé mais fonctionnel', 'windwaker.jpg', 7, 2),
('Neo Geo Pocket', 45.00, 'Neo Geo Pocket Color bleue, écran à réparer', 'neogeopocket.jpg', 3, 1),
('Dreamcast', 35.00, 'SEGA Dreamcast avec câbles, lecteur capricieux', 'dreamcast.jpg', 3, 1);