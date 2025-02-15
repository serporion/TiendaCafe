CREATE DATABASE tienda;
SET NAMES UTF8;
CREATE DATABASE IF NOT EXISTS tienda;
USE tienda;

DROP TABLE IF EXISTS usuarios;
CREATE TABLE IF NOT EXISTS usuarios(
                                       id                INT(255) AUTO_INCREMENT NOT NULL,
    nombre            VARCHAR(100) NOT NULL,
    apellidos         VARCHAR(255),
    email             VARCHAR(255) NOT NULL,
    password          VARCHAR(255) NOT NULL,
    rol               VARCHAR(20),
    fecha_expiracion  DATETIME,
    confirmado        BOOLEAN DEFAULT FALSE,
    token             VARCHAR(255),
    PRIMARY KEY(id),
    UNIQUE(email)
    ) ENGINE=InnoDb DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS categorias;
CREATE TABLE IF NOT EXISTS categorias(
                                         id      INT(255) AUTO_INCREMENT NOT NULL,
    nombre  VARCHAR(100) NOT NULL,
    PRIMARY KEY(id)
    ) ENGINE=InnoDb DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS productos;
CREATE TABLE IF NOT EXISTS productos(
                                        id            INT(255) AUTO_INCREMENT NOT NULL,
    categoria_id  INT(255) NOT NULL,
    nombre        VARCHAR(100) NOT NULL,
    descripcion   TEXT,
    precio        FLOAT(100,2) NOT NULL,
    stock         INT(255) NOT NULL,
    oferta        VARCHAR(2),
    fecha         DATE NOT NULL,
    imagen        VARCHAR(255),
    PRIMARY KEY(id),
    FOREIGN KEY(categoria_id) REFERENCES categorias(id)
    ) ENGINE=InnoDb DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS pedidos;
CREATE TABLE IF NOT EXISTS pedidos(
                                      id              INT(255) AUTO_INCREMENT NOT NULL,
    usuario_id      INT(255) NOT NULL,
    provincia       VARCHAR(100) NOT NULL,
    localidad       VARCHAR(100) NOT NULL,
    direccion       VARCHAR(255) NOT NULL,
    coste           FLOAT(200,2) NOT NULL,
    estado          VARCHAR(20) NOT NULL,
    fecha           DATE,
    hora            TIME,
    pagado          BOOLEAN DEFAULT FALSE,
    transaction_id  VARCHAR(50),
    PRIMARY KEY(id),
    FOREIGN KEY(usuario_id) REFERENCES usuarios(id)
    ) ENGINE=InnoDb DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS lineas_pedidos;
CREATE TABLE IF NOT EXISTS lineas_pedidos(
                                             id              INT(255) AUTO_INCREMENT NOT NULL,
    pedido_id       INT(255) NOT NULL,
    producto_id     INT(255) NOT NULL,
    unidades        INT(255) NOT NULL,
    precio_unitario FLOAT(100,2) NOT NULL,
    PRIMARY KEY(id),
    FOREIGN KEY(pedido_id) REFERENCES pedidos(id),
    FOREIGN KEY(producto_id) REFERENCES productos(id)
    ) ENGINE=InnoDb DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS carritos_guardados;
CREATE TABLE carritos_guardados (
                                    id INT AUTO_INCREMENT PRIMARY KEY,
                                    usuario_id INT NOT NULL,
                                    carrito JSON NOT NULL,
                                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);