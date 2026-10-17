CREATE DATABASE pizzaria_chapada;

USE pizzaria_chapada;

create table usuario(
    id_usu int primary key auto_increment,
    nome varchar(55) not null,	
    email varchar(60) unique not null,
    senha char(64) not null,
    datacad datetime not null,
    nivel_acesso int not null
);


create table cliente (
    id int primary key auto_increment,
    nome varchar(100) not null,
    telefone varchar(20),
    endereco varchar(255),
    id_usuario int,
    foreign key (id_usuario) references usuario(id_usu)
);


create table categoria (
    id int primary key auto_increment,
    nome varchar(100) not null,
    descricao text
);


create table produto (
    id int primary key auto_increment,
    nome varchar(150) not null,
    preco float not null,
    estoque int not null default 0,
    id_categoria int not null,
    foreign key (id_categoria) references categoria(id)
);


create table pedido (
    id int primary key auto_increment,
    data_pedido datetime not null default current_timestamp,
    status varchar(30) not null,
    valor_total float not null default 0.00,
    id_cliente int not null,
    foreign key (id_cliente) references cliente(id)
);


create table item_pedido (
    id int primary key auto_increment,
    id_pedido int not null,
    id_produto int not null,
    quantidade int not null,
    preco_unitario float not null,
    foreign key (id_pedido) references pedido(id),
    foreign key (id_produto) references produto(id)
);


