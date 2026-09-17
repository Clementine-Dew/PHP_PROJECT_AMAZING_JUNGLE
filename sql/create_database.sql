/*
--------------------------------------------------
--                                              --
--           🌵 MY AMAZING JUNGLE  🪴          --
--                                              --
--------------------------------------------------
*/


-- 1. DATABASE
USE master;


GO
IF DB_ID('amazing_jungle') IS NOT NULL
    BEGIN
        ALTER DATABASE amazing_jungle
            SET SINGLE_USER
            WITH ROLLBACK IMMEDIATE;
        DROP DATABASE amazing_jungle;
    END


GO
CREATE DATABASE amazing_jungle;



-- 2. LOGIN AND USER
USE master;

GO
IF SUSER_ID('demo_user') IS NOT NULL
    DROP LOGIN demo_user;


GO
CREATE LOGIN demo_user
    WITH PASSWORD = 'TEST1234=', DEFAULT_DATABASE = amazing_jungle, CHECK_POLICY = OFF;


GO
USE amazing_jungle;


GO
CREATE USER demo_user FOR LOGIN demo_user;


GO
-- MINIMUM RIGHTS : READ AND WRITE ON DATA
ALTER ROLE db_datareader ADD MEMBER demo_user;
ALTER ROLE db_datawriter ADD MEMBER demo_user;


GO
-- 3. TABLES
DROP TABLE IF EXISTS dbo.watering;
DROP TABLE IF EXISTS dbo.my_jungle;
DROP TABLE IF EXISTS dbo.plant;
DROP TABLE IF EXISTS dbo.consumer;
DROP TABLE IF EXISTS dbo.family;


GO
CREATE TABLE dbo.family (
    [id]                    INT             IDENTITY (1, 1)         NOT NULL, 
    [name]                  NVARCHAR(30),
    CONSTRAINT PK_family PRIMARY KEY (id)
);


GO
CREATE TABLE dbo.consumer (
    [id]                    INT             IDENTITY (1, 1)         NOT NULL,
    [lastname]              NVARCHAR(150)   NULL,
    [firstname]             NVARCHAR(50)    NOT NULL,
    [alias]                 NVARCHAR(50)    NOT NULL,
    [email]                 NVARCHAR(255)   NOT NULL                CONSTRAINT UQ_user_email UNIQUE (email),
    [password]              NVARCHAR(255)   NOT NULL,
    [role]                  NVARCHAR(20)    NOT NULL                CONSTRAINT DF_user_role DEFAULT 'user',
    CONSTRAINT PK_consumer PRIMARY KEY (id),
);


GO
CREATE TABLE dbo.plant (
    [id]                    INT              IDENTITY (1, 1)         NOT NULL,
    [name]                  NVARCHAR(50)     NOT NULL,
    [url]                   NVARCHAR(2048)   NOT NULL,
    [location]              NVARCHAR(20)     NULL,
    [watering_frequency]    INT              NOT NULL,
    [note]                  NVARCHAR(1000)   NULL,
    [is_valide]             BIT              NOT NULL,
    [fk_family]             INT              NOT NULL,
    CONSTRAINT PK_plant PRIMARY KEY (id),
    CONSTRAINT FK_plant_family FOREIGN KEY (fk_family) REFERENCES dbo.family (id) ON DELETE CASCADE ON UPDATE NO ACTION
);


GO
CREATE TABLE dbo.my_jungle (
    [id]                    INT              IDENTITY (1, 1)         NOT NULL,
    [fk_plant]              INT              NOT NULL,
    [fk_consumer]           INT              NOT NULL,
    CONSTRAINT PK_my_jungle PRIMARY KEY (id),
    CONSTRAINT FK_my_jungle_plant FOREIGN KEY (fk_plant) REFERENCES dbo.plant (id) ON DELETE CASCADE ON UPDATE NO ACTION,
    CONSTRAINT FK_my_jungle_consumer FOREIGN KEY (fk_consumer) REFERENCES dbo.consumer (id) ON DELETE CASCADE ON UPDATE NO ACTION
);


GO
CREATE TABLE dbo.request (
    [id]                    INT              IDENTITY (1, 1)         NOT NULL,
    [request]               INT              NOT NULL,
    [fk_consumer]           INT              NOT NULL,
    [fk_plant]              INT              NOT NULL,
    CONSTRAINT PK_request PRIMARY KEY (id),
    CONSTRAINT FK_request_consumer FOREIGN KEY (fk_consumer) REFERENCES dbo.consumer (id) ON DELETE CASCADE ON UPDATE NO ACTION,
    CONSTRAINT FK_request_plant FOREIGN KEY (fk_plant) REFERENCES dbo.plant (id) ON DELETE CASCADE ON UPDATE NO ACTION
);


GO
CREATE TABLE dbo.watering (
    [id]                    INT              IDENTITY (1, 1)         NOT NULL,
    [last_watering]         DATETIME2        NULL,
    [next_watering]         DATETIME2        NOT NULL,
    [fk_my_jungle]          INT              NOT NULL,
    CONSTRAINT PK_watering PRIMARY KEY (id),
    CONSTRAINT FK_watering_my_jungle FOREIGN KEY (fk_my_jungle) REFERENCES dbo.my_jungle (id) ON DELETE CASCADE ON UPDATE NO ACTION
    -- GERER LE CALCUL AUTOMATIQUE DE LA DATE D'ARROSAGE
);





GO
-- FILTRED INDEX CREATE
-- BY DEFAULT ON SSMS
SET QUOTED_IDENTIFIER ON;



GO
-- 4. TEST DATA
INSERT INTO dbo.family (
    [name]
)
VALUES 
    (N'Urticacées'), -- Pilea
    (N'Cactacées'), -- Figuier de Barbarie
    (N'Aracées'), -- Monstera Deliciosa / Philodendron
    (N'asparagacées'), -- Yucca
    (N'Commelinacées'); -- Misère


GO
INSERT INTO dbo.plant (
    
)