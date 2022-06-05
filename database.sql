-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Client :  localhost
-- Généré le :  Jeu 26 Octobre 2017 à 13:53
-- Version du serveur :  5.7.19-0ubuntu0.16.04.1
-- Version de PHP :  7.0.22-0ubuntu0.16.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `kintsukuroi`
--

-- --------------------------------------------------------

--
-- Creation of the database
--
DROP DATABASE IF EXISTS kintsukuroi;
CREATE DATABASE kintsukuroi;

USE kintsukuroi;

--
-- Creation of color table
--
CREATE TABLE color (
id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
name VARCHAR(80) NOT NULL);

--
-- Creation of category table
--
CREATE TABLE category (
id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
type VARCHAR(80) NOT NULL);

--
-- Creation of product table
--
CREATE TABLE product (
id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
name VARCHAR(80) NOT NULL,
description TEXT NOT NULL,
price DOUBLE NOT NULL,
image VARCHAR(255) NOT NULL,
quantity INT NOT NULL,
color_id INT NOT NULL,
CONSTRAINT fk_product_color      
FOREIGN KEY (color_id)             
REFERENCES color(id),
category_id INT NOT NULL,
CONSTRAINT fk_product_category      
FOREIGN KEY (category_id)             
REFERENCES category(id));

--
-- Creation of user table
--
CREATE TABLE user (
id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
is_admin BOOL DEFAULT 0,
firstname VARCHAR(80) NOT NULL,
lastname VARCHAR(80) NOT NULL,
password VARCHAR(255) NOT NULL,
email VARCHAR(80) NOT NULL,
address VARCHAR(255) NOT NULL,
phone VARCHAR(10));
-- newsletter BOOL NULL;

--
-- Creation of invoice table
--
CREATE TABLE invoice (
id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
user_id INT NOT NULL,
CONSTRAINT fk_invoice_user
FOREIGN KEY (user_id)
REFERENCES user(id),
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
recipient_firstname VARCHAR(80) NOT NULL,
recipient_lastname VARCHAR(80) NOT NULL,
delivery_address VARCHAR(255) NOT NULL,
payment VARCHAR(80) NOT NULL,
total DOUBLE NOT NULL,
is_treated BOOL DEFAULT 0);

--
-- Creation of invoice_product table
--
CREATE TABLE invoice_product (
id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
invoice_id INT NOT NULL,
CONSTRAINT fk_invoice_product_invoice      
FOREIGN KEY (invoice_id)             
REFERENCES invoice(id)
ON DELETE CASCADE,
product_id INT NOT NULL,
CONSTRAINT fk_invoice_product_product     
FOREIGN KEY (product_id)             
REFERENCES product(id),
quantity INT NOT NULL);

--
-- Insert colors
--
INSERT INTO color (name) VALUES ('Blanc'), ('Noir'), ('Bleu'), ('Beige'), ('Orange'), ('Vert'), ('Rose'), ('Rouge');

--
-- Insert categories
--
INSERT INTO category (type) VALUES ('Assiette'), ('Bol'), ('Tasse'), ('Théière'), ('Vase');

--
-- Insert products
--
INSERT INTO product (name, description, price, image, quantity, color_id, category_id) VALUES 
('Shiro', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc tincidunt risus id odio sodales volutpat. Vivamus vel lacus ex. Nam euismod ultrices odio a tristique. Etiam bibendum, erat in semper consectetur, tellus dui dignissim nisl, vel sagittis nulla lorem eget nulla. Proin sollicitudin enim.', '200', 'https://i.ibb.co/Kbgf2t4/Adobe-Stock-430355929.jpg', '21', '1', '5'),
('Atokuchi', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc tincidunt risus id odio sodales volutpat. Vivamus vel lacus ex. Nam euismod ultrices odio a tristique. Etiam bibendum, erat in semper consectetur, tellus dui dignissim nisl, vel sagittis nulla lorem eget nulla. Proin sollicitudin enim.', '200', 'https://i.ibb.co/0rn0hFL/Adobe-Stock-463917493.jpg', '15', '4', '5'),
('Akibine', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc tincidunt risus id odio sodales volutpat. Vivamus vel lacus ex. Nam euismod ultrices odio a tristique. Etiam bibendum, erat in semper consectetur, tellus dui dignissim nisl, vel sagittis nulla lorem eget nulla. Proin sollicitudin enim.', '250', 'https://i.ibb.co/mFHByRh/Adobe-Stock-403407802.jpg', '14', '8', '5'),
('Wan', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc tincidunt risus id odio sodales volutpat. Vivamus vel lacus ex. Nam euismod ultrices odio a tristique. Etiam bibendum, erat in semper consectetur, tellus dui dignissim nisl, vel sagittis nulla lorem eget nulla. Proin sollicitudin enim.', '250', 'https://i.ibb.co/CJprkVr/Adobe-Stock-404511640.jpg', '17', '3', '5'),
('Binbiiru', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc tincidunt risus id odio sodales volutpat. Vivamus vel lacus ex. Nam euismod ultrices odio a tristique. Etiam bibendum, erat in semper consectetur, tellus dui dignissim nisl, vel sagittis nulla lorem eget nulla. Proin sollicitudin enim.', '200', 'https://i.ibb.co/RgfqC1k/Adobe-Stock-403407927.jpg', '7', '8', '5'),
('Atokuchi', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc tincidunt risus id odio sodales volutpat. Vivamus vel lacus ex. Nam euismod ultrices odio a tristique. Etiam bibendum, erat in semper consectetur, tellus dui dignissim nisl, vel sagittis nulla lorem eget nulla. Proin sollicitudin enim.', '300', 'https://i.ibb.co/92JLmMX/Adobe-Stock-463917635.jpg', '5', '2', '5'),
('Soyokaze', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc tincidunt risus id odio sodales volutpat. Vivamus vel lacus ex. Nam euismod ultrices odio a tristique. Etiam bibendum, erat in semper consectetur, tellus dui dignissim nisl, vel sagittis nulla lorem eget nulla. Proin sollicitudin enim.', '300', 'https://i.ibb.co/2F5qDhn/Adobe-Stock-403406906.jpg', '33', '3', '1'),
('Ezara', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc tincidunt risus id odio sodales volutpat. Vivamus vel lacus ex. Nam euismod ultrices odio a tristique. Etiam bibendum, erat in semper consectetur, tellus dui dignissim nisl, vel sagittis nulla lorem eget nulla. Proin sollicitudin enim.', '150', 'https://i.ibb.co/CbqXnV8/Adobe-Stock-463918048.jpg', '30', '3', '1'),
('Sakkazu', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc tincidunt risus id odio sodales volutpat. Vivamus vel lacus ex. Nam euismod ultrices odio a tristique. Etiam bibendum, erat in semper consectetur, tellus dui dignissim nisl, vel sagittis nulla lorem eget nulla. Proin sollicitudin enim.', '150', 'https://i.ibb.co/dDk0SGj/Adobe-Stock-394748470.jpg', '30', '6', '1'),
('Akairo', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc tincidunt risus id odio sodales volutpat. Vivamus vel lacus ex. Nam euismod ultrices odio a tristique. Etiam bibendum, erat in semper consectetur, tellus dui dignissim nisl, vel sagittis nulla lorem eget nulla. Proin sollicitudin enim.', '200', 'https://i.ibb.co/0qXHDXq/Adobe-Stock-404511794.jpg', '30', '8', '1'),
('Kuroi', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc tincidunt risus id odio sodales volutpat. Vivamus vel lacus ex. Nam euismod ultrices odio a tristique. Etiam bibendum, erat in semper consectetur, tellus dui dignissim nisl, vel sagittis nulla lorem eget nulla. Proin sollicitudin enim.', '210', 'https://i.ibb.co/vVF8hqj/Adobe-Stock-463918140.jpg', '30', '3', '1'),
('Undo', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc tincidunt risus id odio sodales volutpat. Vivamus vel lacus ex. Nam euismod ultrices odio a tristique. Etiam bibendum, erat in semper consectetur, tellus dui dignissim nisl, vel sagittis nulla lorem eget nulla. Proin sollicitudin enim.', '210', 'https://i.ibb.co/cLzDK8g/Adobe-Stock-369709001.jpg', '30', '3', '1'),
('Konoha','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc tincidunt risus id odio sodales volutpat. Vivamus vel lacus ex. Nam euismod ultrices odio a tristique. Etiam bibendum, erat in semper consectetur, tellus dui dignissim nisl, vel sagittis nulla lorem eget nulla. Proin sollicitudin enim.', '70', 'https://i.ibb.co/fC0xkKW/Adobe-Stock-424518341.jpg', '15', '7', '3'),
('Kappu','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc tincidunt risus id odio sodales volutpat. Vivamus vel lacus ex. Nam euismod ultrices odio a tristique. Etiam bibendum, erat in semper consectetur, tellus dui dignissim nisl, vel sagittis nulla lorem eget nulla. Proin sollicitudin enim.', '70', 'https://i.ibb.co/J3rFFFF/Adobe-Stock-369709623.jpg', '15', '5', '3'),
('Orenjisoda','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc tincidunt risus id odio sodales volutpat. Vivamus vel lacus ex. Nam euismod ultrices odio a tristique. Etiam bibendum, erat in semper consectetur, tellus dui dignissim nisl, vel sagittis nulla lorem eget nulla. Proin sollicitudin enim.', '70', 'https://i.ibb.co/P5R0L9H/Adobe-Stock-345331189.jpg', '15', '5', '3'),
('Bobina','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc tincidunt risus id odio sodales volutpat. Vivamus vel lacus ex. Nam euismod ultrices odio a tristique. Etiam bibendum, erat in semper consectetur, tellus dui dignissim nisl, vel sagittis nulla lorem eget nulla. Proin sollicitudin enim.', '70', 'https://i.ibb.co/6B06b8x/Adobe-Stock-368638765.jpg', '15', '3', '3'),
('Kuria','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc tincidunt risus id odio sodales volutpat. Vivamus vel lacus ex. Nam euismod ultrices odio a tristique. Etiam bibendum, erat in semper consectetur, tellus dui dignissim nisl, vel sagittis nulla lorem eget nulla. Proin sollicitudin enim.', '70', 'https://i.ibb.co/HGhQ6YP/Adobe-Stock-369709599.jpg', '15', '1', '3'),
('Kizashi','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc tincidunt risus id odio sodales volutpat. Vivamus vel lacus ex. Nam euismod ultrices odio a tristique. Etiam bibendum, erat in semper consectetur, tellus dui dignissim nisl, vel sagittis nulla lorem eget nulla. Proin sollicitudin enim.', '70', 'https://i.ibb.co/XjPXt4f/Adobe-Stock-368638154.jpg', '15', '4', '3'),
('Hana', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc tincidunt risus id odio sodales volutpat. Vivamus vel lacus ex. Nam euismod ultrices odio a tristique. Etiam bibendum, erat in semper consectetur, tellus dui dignissim nisl, vel sagittis nulla lorem eget nulla. Proin sollicitudin enim.', '150', 'https://i.ibb.co/Kxn0JTF/Adobe-Stock-369709805.jpg', '19', '3', '2'),
('Kusabana', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc tincidunt risus id odio sodales volutpat. Vivamus vel lacus ex. Nam euismod ultrices odio a tristique. Etiam bibendum, erat in semper consectetur, tellus dui dignissim nisl, vel sagittis nulla lorem eget nulla. Proin sollicitudin enim.', '160', 'https://i.ibb.co/2dNrZXk/Adobe-Stock-424518417.jpg', '19', '2', '2'),
('Bolinette', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc tincidunt risus id odio sodales volutpat. Vivamus vel lacus ex. Nam euismod ultrices odio a tristique. Etiam bibendum, erat in semper consectetur, tellus dui dignissim nisl, vel sagittis nulla lorem eget nulla. Proin sollicitudin enim.', '160', 'https://i.ibb.co/yPD1Sf6/Adobe-Stock-368638568.jpg', '19', '4', '2'),
('Kokoro', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc tincidunt risus id odio sodales volutpat. Vivamus vel lacus ex. Nam euismod ultrices odio a tristique. Etiam bibendum, erat in semper consectetur, tellus dui dignissim nisl, vel sagittis nulla lorem eget nulla. Proin sollicitudin enim.', '150', 'https://i.ibb.co/SmSXBCr/Adobe-Stock-368637856.jpg', '19', '2', '2'),
('Beiju', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc tincidunt risus id odio sodales volutpat. Vivamus vel lacus ex. Nam euismod ultrices odio a tristique. Etiam bibendum, erat in semper consectetur, tellus dui dignissim nisl, vel sagittis nulla lorem eget nulla. Proin sollicitudin enim.', '150', 'https://i.ibb.co/rbQPcyX/Adobe-Stock-369709503.jpg', '19', '5', '5'),
('Bobine', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc tincidunt risus id odio sodales volutpat. Vivamus vel lacus ex. Nam euismod ultrices odio a tristique. Etiam bibendum, erat in semper consectetur, tellus dui dignissim nisl, vel sagittis nulla lorem eget nulla. Proin sollicitudin enim.', '350', 'https://i.ibb.co/wrSGvQX/Adobe-Stock-403408077.jpg', '5', '1', '4'),
('Funanori', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc tincidunt risus id odio sodales volutpat. Vivamus vel lacus ex. Nam euismod ultrices odio a tristique. Etiam bibendum, erat in semper consectetur, tellus dui dignissim nisl, vel sagittis nulla lorem eget nulla. Proin sollicitudin enim.', '360', 'https://i.ibb.co/fxtF5xr/Adobe-Stock-403407086.jpg', '7', '3', '4'),
('Dobin', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc tincidunt risus id odio sodales volutpat. Vivamus vel lacus ex. Nam euismod ultrices odio a tristique. Etiam bibendum, erat in semper consectetur, tellus dui dignissim nisl, vel sagittis nulla lorem eget nulla. Proin sollicitudin enim.', '345', 'https://i.ibb.co/J2kG35S/Adobe-Stock-403407110.jpg', '7', '3', '4'),
('Kyuusu', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc tincidunt risus id odio sodales volutpat. Vivamus vel lacus ex. Nam euismod ultrices odio a tristique. Etiam bibendum, erat in semper consectetur, tellus dui dignissim nisl, vel sagittis nulla lorem eget nulla. Proin sollicitudin enim.', '400', 'https://i.ibb.co/fpcZVGV/Adobe-Stock-368638580.jpg', '14', '8', '4');

--
-- Insert users
--
INSERT INTO user (is_admin, firstname, lastname, password, email, address, phone) VALUES
(1, 'Lucas', 'Turtle', 'c4c7794404013c1482e6ec46735eaffc', 'lucas.turtle@test.com', '12 rue de la mer', 0655233322), 
(0, 'Robyn', 'Doggy', 'b33e14475f929fc18942e62c14ed207b', 'robyn.doggy@test.com', '51 avenue de la niche', 0655233322), 
(0, 'Obi', 'Meow', 'e9812d230eba9a0839b2f77239957248', 'obi.meow@test.com', '25 avenue du saumon', 0655233322), 
(0, 'Franky', 'Whiskers', 'bfe5114fd67a4e78f962c07385b1d9a6', 'franky.whiskers@test.com', '4 impasse des petites bouchées de canard', 0655233322);
