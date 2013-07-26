-- phpMyAdmin SQL Dump
-- version 3.5.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 28, 2013 at 04:12 PM
-- Server version: 5.5.12
-- PHP Version: 5.4.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


-- --------------------------------------------------------

--
-- Dumping data for table `translation`
--

INSERT INTO `translation` (`id`, `language_id`, `table`, `row_id`, `translation`) VALUES
(NULL, 145, 'collabpref', 1, 'Plačano delo'),
(NULL, 145, 'collabpref', 2, 'Delovni vložek'),
(NULL, 145, 'collabpref', 3, 'Enakovreden investitor'),
(NULL, 145, 'collabpref', 4, 'Investitor'),
(NULL, 145, 'collabpref', 5, 'Prostovoljec'),
(NULL, 145, 'membertype', 1, 'Lastnik'),
(NULL, 145, 'membertype', 2, 'Član'),
(NULL, 145, 'membertype', 3, 'Kandidat'),
(NULL, 145, 'idea_status', 1, 'Ideja'),
(NULL, 145, 'idea_status', 2, 'Poslovni načrt'),
(NULL, 145, 'idea_status', 3, 'Prototip'),
(NULL, 145, 'idea_status', 4, 'Stranke ki plačujejo'),
(NULL, 145, 'idea_status', 5, 'Rast'),
(NULL, 145, 'available', 8, 'Vikendi'),
(NULL, 145, 'available', 20, 'Polovični delovni čas'),
(NULL, 145, 'available', 40, 'Polni delovni čas'),
(NULL, 145, 'skillset', 1, 'Računovodstvo'),
(NULL, 145, 'skillset', 2, 'Letalstvo'),
(NULL, 145, 'skillset', 3, 'Izvensodna poravnava'),
(NULL, 145, 'skillset', 4, 'Alternativna medicina'),
(NULL, 145, 'skillset', 5, 'Animacija'),
(NULL, 145, 'skillset', 6, 'Oblačila in moda'),
(NULL, 145, 'skillset', 7, 'Arhitektura'),
(NULL, 145, 'skillset', 8, 'Domača in umetnostna obrt'),
(NULL, 145, 'skillset', 9, 'Avtomobilska industrija'),
(NULL, 145, 'skillset', 10, 'Letalstvo & vesoljska industrija'),
(NULL, 145, 'skillset', 11, 'Bančništvo'),
(NULL, 145, 'skillset', 12, 'Biotehnologija'),
(NULL, 145, 'skillset', 13, 'Radio in televizija'),
(NULL, 145, 'skillset', 14, 'Gradbeni materiali'),
(NULL, 145, 'skillset', 15, 'Poslovna in pisarniška oprema'),
(NULL, 145, 'skillset', 16, 'Kapitalski trgi'),
(NULL, 145, 'skillset', 17, 'Kemikalije'),
(NULL, 145, 'skillset', 18, 'Civilna in socialna organizacija'),
(NULL, 145, 'skillset', 19, 'Stavbarstvo'),
(NULL, 145, 'skillset', 20, 'Komercialne nepremičnine'),
(NULL, 145, 'skillset', 21, 'Varnost omrežij'),
(NULL, 145, 'skillset', 22, 'Računalniške igre'),
(NULL, 145, 'skillset', 23, 'Strojna oprema'),
(NULL, 145, 'skillset', 24, 'Računalniška omrežja'),
(NULL, 145, 'skillset', 25, 'Programska oprema'),
(NULL, 145, 'skillset', 26, 'Gradnja'),
(NULL, 145, 'skillset', 27, 'Potrošniška elektronika'),
(NULL, 145, 'skillset', 28, 'Potrošniške dobrine'),
(NULL, 145, 'skillset', 29, 'Potrošniške storitve'),
(NULL, 145, 'skillset', 30, 'Kozmetika'),
(NULL, 145, 'skillset', 31, 'Mleko in mlečni izdelki'),
(NULL, 145, 'skillset', 32, 'Obramba in vesoljski program'),
(NULL, 145, 'skillset', 33, 'Oblikovanje'),
(NULL, 145, 'skillset', 34, 'Management izobraževanja'),
(NULL, 145, 'skillset', 35, 'E-učenje'),
(NULL, 145, 'skillset', 36, 'Proizvodnja elektronike'),
(NULL, 145, 'skillset', 37, 'Zabavna industrija'),
(NULL, 145, 'skillset', 38, 'Okoljske storitve'),
(NULL, 145, 'skillset', 39, 'Organizacija dogodkov'),
(NULL, 145, 'skillset', 41, 'Vzdrževalne storitve'),
(NULL, 145, 'skillset', 42, 'Kmetijstvo'),
(NULL, 145, 'skillset', 43, 'Finančne storitve'),
(NULL, 145, 'skillset', 44, 'Likovna umetnost'),
(NULL, 145, 'skillset', 45, 'Ribolov'),
(NULL, 145, 'skillset', 46, 'Hrana in pijača'),
(NULL, 145, 'skillset', 47, 'Proizvodnja hrane'),
(NULL, 145, 'skillset', 48, 'Zbiranje sredstev'),
(NULL, 145, 'skillset', 49, 'Pohištvo'),
(NULL, 145, 'skillset', 50, 'Igralništvo in kazinoji'),
(NULL, 145, 'skillset', 51, 'Steklo, keramika in beton'),
(NULL, 145, 'skillset', 52, 'Javna uprava'),
(NULL, 145, 'skillset', 53, 'Odnosi z javno upravo'),
(NULL, 145, 'skillset', 54, 'Grafično oblikovanje'),
(NULL, 145, 'skillset', 55, 'Zdravje, wellness in fitness'),
(NULL, 145, 'skillset', 56, 'Visokošolska izobrazba'),
(NULL, 145, 'skillset', 57, 'Zdravstvena nega in oskrba'),
(NULL, 145, 'skillset', 59, 'Človeški viri'),
(NULL, 145, 'skillset', 60, 'Uvoz in izvoz'),
(NULL, 145, 'skillset', 62, 'Industrijska avtomatizacija'),
(NULL, 145, 'skillset', 63, 'Informacijske storitve'),
(NULL, 145, 'skillset', 64, 'Informacijska tehnologija in storitve'),
(NULL, 145, 'skillset', 65, 'Zavarovalništvo'),
(NULL, 145, 'skillset', 66, 'Mednarodni odnosi'),
(NULL, 145, 'skillset', 67, 'Mednarodna trgovina in razvoj'),
(NULL, 145, 'skillset', 68, 'Internet'),
(NULL, 145, 'skillset', 69, 'Investicijsko bančništvo'),
(NULL, 145, 'skillset', 70, 'Investicijski management'),
(NULL, 145, 'skillset', 71, 'Sodstvo'),
(NULL, 145, 'skillset', 72, 'Kazenski pregon'),
(NULL, 145, 'skillset', 74, 'Pravne storitve'),
(NULL, 145, 'skillset', 76, 'Prosti čas, potovanja in turizem'),
(NULL, 145, 'skillset', 77, 'Knjižnjice'),
(NULL, 145, 'skillset', 78, 'Logistika in dobaviteljske verige'),
(NULL, 145, 'skillset', 79, 'Luksuzne dobrine in nakit'),
(NULL, 145, 'skillset', 80, 'Stroji'),
(NULL, 145, 'skillset', 81, 'Management svetovanje'),
(NULL, 145, 'skillset', 82, 'Pomorstvo'),
(NULL, 145, 'skillset', 83, 'Marketing in oglaševanje'),
(NULL, 145, 'skillset', 84, 'Tržne raziskave'),
(NULL, 145, 'skillset', 85, 'Mehanski in industrijski inžiniring'),
(NULL, 145, 'skillset', 86, 'Medijska produkcija'),
(NULL, 145, 'skillset', 87, 'Medicinske naprave'),
(NULL, 145, 'skillset', 88, 'Medicina'),
(NULL, 145, 'skillset', 89, 'Mentalna zdravstvena nega'),
(NULL, 145, 'skillset', 90, 'Vojska'),
(NULL, 145, 'skillset', 91, 'Rudarstvo in kovine'),
(NULL, 145, 'skillset', 92, 'Filmi'),
(NULL, 145, 'skillset', 93, 'Muzeji in institucije'),
(NULL, 145, 'skillset', 94, 'Glasba'),
(NULL, 145, 'skillset', 95, 'Nanotehnologija'),
(NULL, 145, 'skillset', 96, 'Časopisi'),
(NULL, 145, 'skillset', 97, 'Management neprofitne organizacije'),
(NULL, 145, 'skillset', 98, 'Nafta in energetika'),
(NULL, 145, 'skillset', 99, 'Spletni mediji'),
(NULL, 145, 'skillset', 100, 'Outsourcing'),
(NULL, 145, 'skillset', 101, 'Dostava paketov'),
(NULL, 145, 'skillset', 102, 'Pakiranje in kontejnerji'),
(NULL, 145, 'skillset', 103, 'Papir in izdelki iz lesa'),
(NULL, 145, 'skillset', 105, 'Farmacija'),
(NULL, 145, 'skillset', 106, 'Filantrofija'),
(NULL, 145, 'skillset', 107, 'Fotografija'),
(NULL, 145, 'skillset', 108, 'Plastika'),
(NULL, 145, 'skillset', 109, 'Politična organizacija'),
(NULL, 145, 'skillset', 110, 'Osnovnošolska/gimnazijska izobrazba'),
(NULL, 145, 'skillset', 111, 'Tisk'),
(NULL, 145, 'skillset', 112, 'Poslovno svetovanje'),
(NULL, 145, 'skillset', 113, 'Razvoj programov'),
(NULL, 145, 'skillset', 114, 'Javna politika'),
(NULL, 145, 'skillset', 115, 'Odnosi z javnostmi in komunikacije'),
(NULL, 145, 'skillset', 116, 'Javna varnost'),
(NULL, 145, 'skillset', 117, 'Založništvo'),
(NULL, 145, 'skillset', 118, 'Železniška proizvodnja'),
(NULL, 145, 'skillset', 119, 'Rančing'),
(NULL, 145, 'skillset', 120, 'Nepremičnine'),
(NULL, 145, 'skillset', 121, 'Rekreacijski objekti in storitve'),
(NULL, 145, 'skillset', 122, 'Verske institucije'),
(NULL, 145, 'skillset', 123, 'Okolje in obnovljivi viri'),
(NULL, 145, 'skillset', 124, 'Raziskave'),
(NULL, 145, 'skillset', 125, 'Restavracije'),
(NULL, 145, 'skillset', 126, 'Trgovina na drobno'),
(NULL, 145, 'skillset', 127, 'Varovanje in preiskave'),
(NULL, 145, 'skillset', 128, 'Polprevodniki'),
(NULL, 145, 'skillset', 129, 'Ladjarstvo'),
(NULL, 145, 'skillset', 130, 'Športna oprema'),
(NULL, 145, 'skillset', 131, 'Športi'),
(NULL, 145, 'skillset', 132, 'Kadrovanje'),
(NULL, 145, 'skillset', 133, 'Supermarketi'),
(NULL, 145, 'skillset', 134, 'Telekomunikacije'),
(NULL, 145, 'skillset', 135, 'Tekstil'),
(NULL, 145, 'skillset', 137, 'Tobak'),
(NULL, 145, 'skillset', 138, 'Prevodi in lokalizacija'),
(NULL, 145, 'skillset', 139, 'Transport, tovornjaki in železnica'),
(NULL, 145, 'skillset', 141, 'Tvegani in privatni kapital'),
(NULL, 145, 'skillset', 142, 'Veterinarstvo'),
(NULL, 145, 'skillset', 143, 'Skladiščenje'),
(NULL, 145, 'skillset', 144, 'Prodaja na debelo'),
(NULL, 145, 'skillset', 145, 'Vino in žganja'),
(NULL, 145, 'skillset', 146, 'Brezžična omrežja'),
(NULL, 145, 'skillset', 147, 'Pisateljstvo in uredništvo');


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
