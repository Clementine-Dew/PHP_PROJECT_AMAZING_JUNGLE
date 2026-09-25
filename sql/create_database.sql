/*
--------------------------------------------------
--                                              --
--           🌵 MY AMAZING JUNGLE 🪴           --
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
    WITH PASSWORD = 'Test1234=', DEFAULT_DATABASE = amazing_jungle, CHECK_POLICY = OFF;


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
    [id]                                    INT                 IDENTITY (1, 1)         NOT NULL, 
    [name]                                  NVARCHAR(30)        NOT NULL,
    CONSTRAINT PK_family PRIMARY KEY (id)
);


GO
CREATE TABLE dbo.consumer (
    [id]                                    INT                 IDENTITY (1, 1)         NOT NULL,
    [lastname]                              NVARCHAR(150)       NULL,
    [firstname]                             NVARCHAR(50)        NOT NULL,
    [alias]                                 NVARCHAR(50)        NOT NULL,
    [email]                                 NVARCHAR(255)       NOT NULL                CONSTRAINT UQ_user_email UNIQUE (email),
    [password]                              NVARCHAR(255)       NOT NULL,
    [role]                                  NVARCHAR(20)        NOT NULL                CONSTRAINT DF_user_role DEFAULT 'user',
    CONSTRAINT PK_consumer PRIMARY KEY (id)
);


GO
CREATE TABLE dbo.plant (
    [id]                                    INT                 IDENTITY (1, 1)         NOT NULL,
    [name]                                  NVARCHAR(50)        NOT NULL,
    [link]                                  NVARCHAR(255)       NOT NULL,
    [watering_frequency_spring_summer]      INT                 NOT NULL,
    [watering_frequency_autumn_winter]      INT                 NOT NULL,
    [sign_lack_water]                       NVARCHAR(150)       NULL,
    [sign_excessive_water]                  NVARCHAR(150)       NULL,
    [exposure]                              NVARCHAR(150)       NULL,
    [temperature_min]                       SMALLINT            NULL,
    [temperature_max]                       SMALLINT            NULL,
    [sign_lack_light]                       NVARCHAR(150)       NULL,
    [sign_excessive_light]                  NVARCHAR(150)       NULL,
    [note]                                  NVARCHAR(1000)      NULL,
    [fk_family]                             INT                 NOT NULL,
    CONSTRAINT PK_plant PRIMARY KEY (id),
    CONSTRAINT FK_plant_family FOREIGN KEY (fk_family) REFERENCES dbo.family (id) ON DELETE CASCADE ON UPDATE NO ACTION,
);


GO
CREATE TABLE dbo.my_jungle (
    [id]                                    INT                 IDENTITY (1, 1)         NOT NULL,
    [location]                              NVARCHAR(50)        NULL,
    [fk_plant]                              INT                 NOT NULL,
    [fk_consumer]                           INT                 NOT NULL,
    CONSTRAINT PK_my_jungle PRIMARY KEY (id),
    CONSTRAINT FK_my_jungle_plant FOREIGN KEY (fk_plant) REFERENCES dbo.plant (id) ON DELETE NO ACTION ON UPDATE NO ACTION,
    CONSTRAINT FK_my_jungle_consumer FOREIGN KEY (fk_consumer) REFERENCES dbo.consumer (id) ON DELETE CASCADE ON UPDATE NO ACTION
);



GO
CREATE TABLE dbo.watering (
    [id]                                    INT                 IDENTITY (1, 1)         NOT NULL,
    [watered_date]                          DATETIME2           NOT NULL,
    [fk_my_jungle]                          INT                 NOT NULL,
    CONSTRAINT PK_watering PRIMARY KEY (id),
    CONSTRAINT FK_watering_my_jungle FOREIGN KEY (fk_my_jungle) REFERENCES dbo.my_jungle (id) ON DELETE CASCADE ON UPDATE NO ACTION
);



GO
-- 4. TEST DATA
INSERT INTO dbo.family (
    [name]
)
VALUES 
    (N'Urticacées'), -- Pilea
    (N'Cactacées'), -- Figuier de Barbarie
    (N'Aracées'), -- Monstera Deliciosa / Philodendron
    (N'Asparagacées'), -- Yucca
    (N'Marantacées'), -- Calathea Lancifolia
    (N'Commelinacées'), -- Misère
    (N'Oxalidacées'); -- Oxalis


GO
INSERT INTO dbo.plant (
    [name],
    [link],
    [watering_frequency_spring_summer],
    [watering_frequency_autumn_winter],
    [sign_lack_water],
    [sign_excessive_water],
    [exposure],
    [temperature_min],
    [temperature_max],
    [sign_lack_light],
    [sign_excessive_light],
    [note],
    [fk_family]
)
VALUES
    (N'Calathea Lancifolia', N'assets/pictures/calathea_lancifolia.jpg', 10, 15, N'Feuilles enroulées, mates ou retombantes ; substrat très sec en profondeur.', N'Jaunissement diffus, taches noires, odeur de terre détrempées.', N'Lumière indirecte, est ou ouest filtré.', 18, 27, N'Croissance ralentie, feuilles moins panachées, port lâche.', N'Bords brunis, feuillage qui pâlit ou taches de brulure.',  N'La Calathea Lancifolia préfère une humidité ambiante élevée 50–70 %. Adaptez l’arrosage à la taille du pot, la saison et l’humidité ambiante. Préférez un arrosage profond et ponctuel plutôt que des petites doses fréquentes. Rempotez lorsque la plante est à l’étroit ou tous les 1–2 ans pour les sujets jeunes. Utilisez un substrat léger et riche en matière organique avec bon drainage. Mélange recommandé : 50 % terreau pour plantes d’intérieur + 30 % écorce ou fibre de coco + 20 % perlite. Choisissez un pot 2–4 cm plus grand que le précédent.', 5),

    (N'Pilea Peperomioides', N'assets/pictures/pilea_peperomioides_v.jpg', 7, 15, N'Feuilles enroulées, mates ou retombantes ; substrat très sec en profondeur.', N'Jaunissement diffus, taches noires sur la base, odeur de terre détrempée', N'Lumière vive et indirecte', 18, 27, N'Tiges allongées, feuilles espacées, perte de pigments.', N'Brûlures brunes sur les bords, feuilles recroquevillées, teinte pâlie.',  N'La Pilea préfère une humidité ambiante de 50–70 %. Les températures inférieures à 12 °C ralentissent la croissance et favorisent le stress. Rempotez lorsque la plante est à l’étroit ou tous les 12–24 mois. Utilisez un substrat drainant (terreau pour plantes d’intérieur allégé) et un pot avec trou de drainage. Privilégiez un pot + 2–4 cm de diamètre par rapport à l’ancien pot. Ratio substrat : 60 % terreau universel, 30 % perlite, 10 % compost mûr.', 1),

    (N'Monstera Deliciosa', N'assets/pictures/monstera_deliciosa_v.jpg', 10, 15, N'Feuilles enroulées, mates ou retombantes ; substrat très sec en profondeur.', N'Jaunissement diffus, taches noires, odeur de terre détrempée.', N'Lumière indirecte à lumineuse, éviter le soleil direct.', 18, 27, N'Nouvelles feuilles petites, bordures non fendillées, croissance lente et couleur pâle.', N'Taches brunes, brûlures sur les bords, feuilles jaunes au contact du soleil direct fort.', N'Le Monstera aime un substrat aérien et drainant. Mélange conseillé : terreau universel 50 % + écorces de pin ou sphaigne grossière 25 % + perlite 25 %. Rempotez au printemps tous les 2–3 ans ou quand les racines sortent du pot. Choisissez un pot 2–4 cm plus large et assurez un bon trou de drainage.', 3),

    (N'Strelitzia Reginae (oiseau du paradis)', N'assets/pictures/strelitzia_reginae_v.jpg', 5, 8, NULL, NULL, N'Lumière vive et directe', 18, 28, NULL, NULL, N'Laissez sécher la couche supérieure du sol entre les arrosages. Ses racines charnues sont sujettes à la pourriture quand elles sont trop humides. Veillez à tourner régulièrement votre Strelitzia. Le strelitzia préfère un sol bien drainant, riche en nutriments et dont le pH est compris entre 6,0 et 7,0. Pour créer le meilleur mélange de terre, il faut inclure de la mousse de tourbe, de la perlite et de la vermiculite.', 3);
    
    -- (N'Oxalis', N'assets/pictures/oxalis_v.jpg', 7, 15, N'Tiges ramollies et tombantes. Feuilles sèches : Les extrémités ou les folioles deviennent cassantes, jaunissent ou sèchent sur les bords.', N'Jaunissement des feuilles. Tiges molles ou pourries.', N'', 7)