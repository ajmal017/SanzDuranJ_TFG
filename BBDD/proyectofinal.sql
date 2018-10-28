-- phpMyAdmin SQL Dump
-- version 4.8.0.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 28-10-2018 a las 19:31:44
-- Versión del servidor: 10.1.32-MariaDB
-- Versión de PHP: 7.1.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `proyectofinal`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `datamining`
--

CREATE TABLE `datamining` (
  `PK_DM` int(11) NOT NULL,
  `NAME` varchar(15) NOT NULL,
  `DESCRIPCION` varchar(200) NOT NULL,
  `FK_DM` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `datamining`
--

INSERT INTO `datamining` (`PK_DM`, `NAME`, `DESCRIPCION`, `FK_DM`) VALUES
(1, 'KMEANS3D', '', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `indicadores`
--

CREATE TABLE `indicadores` (
  `PK_INDICADORE` int(11) NOT NULL,
  `NAME` varchar(20) NOT NULL,
  `FK_INDICADORE` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `indicadores`
--

INSERT INTO `indicadores` (`PK_INDICADORE`, `NAME`, `FK_INDICADORE`) VALUES
(1, 'MACD', 0),
(2, 'SMA', 0),
(3, 'EMA', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mlalgos`
--

CREATE TABLE `mlalgos` (
  `PK_ML` int(11) NOT NULL,
  `NAME` varchar(20) NOT NULL,
  `DESCRIPCION` varchar(1000) NOT NULL,
  `FK_ML` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `mlalgos`
--

INSERT INTO `mlalgos` (`PK_ML`, `NAME`, `DESCRIPCION`, `FK_ML`) VALUES
(1, 'Clusterización', '', 0),
(2, 'Regresión', '', 0),
(3, 'KMeans', '', 1),
(4, 'Least Squares', '', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tickers`
--

CREATE TABLE `tickers` (
  `PK_TICKER` int(11) NOT NULL,
  `SYMBOL` varchar(30) NOT NULL,
  `NAME` varchar(50) NOT NULL,
  `TIINGO_NAME` varchar(50) NOT NULL,
  `CURRENCY` varchar(11) NOT NULL,
  `DESCRIPCION` varchar(100) NOT NULL,
  `FK_TIPO` int(11) NOT NULL,
  `EXCHANGE` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `tickers`
--

INSERT INTO `tickers` (`PK_TICKER`, `SYMBOL`, `NAME`, `TIINGO_NAME`, `CURRENCY`, `DESCRIPCION`, `FK_TIPO`, `EXCHANGE`) VALUES
(3, 'DAX', 'Horizons dax germany etf', 'dax', 'USD', '', 1, 'NASDAQ'),
(7, 'DIA', 'SPDR Dow Jones Industrial Average ETF', 'dia', 'USD', '', 1, 'NYSE ARCA'),
(19, 'AMLP', 'Alerian MLP ETF', 'amlp', 'USD', '', 1, 'NYSE ARCA'),
(20, 'DBEF', 'Deutsche X-trackers MSCI EAFE Hedged Eq', 'dbef', 'USD', '', 1, 'NYSE ARCA'),
(21, 'DUST', 'Direxion Daily Gold Miners Bear 3X ETF', 'dust', 'USD', '', 1, 'NYSE ARCA'),
(22, 'OIL', 'iPath S&P GSCI Crude Oil TR ETN', 'oil', 'USD', '', 1, 'NYSE ARCA'),
(23, 'QQQ', 'PowerShares QQQ Trust, Series 1 (ETF)', 'qqq', 'USD', '', 1, 'NASDAQ'),
(24, 'SPY', 'SPDR S&P 500 ETF Trust', 'spy', 'USD', '', 1, 'NYSE ARCA'),
(25, 'USO', 'United States Oil Fund LP (ETF)', 'uso', 'USD', '', 1, 'NYSE'),
(26, 'GLD', 'SPDR Gold Shares', 'gld', 'USD', '', 1, 'NYSE ARCA'),
(27, 'AAPL', 'NASDAQ: AAPL', 'aapl', 'USD', 'https://api.tiingo.com/tiingo/daily/aapl', 3, 'NASDAQ'),
(28, 'EEM', 'iShares MSCI Emerging Markets Indx (ETF)', 'eem', 'USD', '', 1, 'NASDAQ'),
(30, 'XLF', 'Financial Select Sector SPDR Fund', 'xlf', 'USD', '', 1, 'NYSE ARCA'),
(31, 'TVIX', 'Cr SUISSE AG NA/VELOCITY SHS DAILY', 'tvix', 'USD', '', 1, 'NASDAQ'),
(32, 'AMZN', 'Amazon.com, Inc.', 'amzn', 'USD', '', 3, 'NASDAQ'),
(33, 'FB', 'Facebook, Inc. Common Stock', 'fb', 'USD', '', 3, 'NASDAQ'),
(34, 'TWX', 'Time Warner Inc', 'twx', 'USD', '', 3, 'NYSE'),
(35, 'GOOGL', 'Alphabet Inc Class A', 'googl', 'USD', '', 3, 'NASDAQ'),
(36, 'MSFT', 'Microsoft Corporation', 'msft', 'USD', '', 3, 'NASDAQ');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo`
--

CREATE TABLE `tipo` (
  `PK_TIPO` int(11) NOT NULL,
  `TIPO` int(11) NOT NULL,
  `NOMBRE` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `tipo`
--

INSERT INTO `tipo` (`PK_TIPO`, `TIPO`, `NOMBRE`) VALUES
(1, 1, 'ETF'),
(2, 2, 'Mutual Fund'),
(3, 3, 'Stock');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tstreat`
--

CREATE TABLE `tstreat` (
  `PK_TS` int(11) NOT NULL,
  `NAME` varchar(20) NOT NULL,
  `DESCRIPCION` varchar(1000) NOT NULL,
  `FK_TS` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `tstreat`
--

INSERT INTO `tstreat` (`PK_TS`, `NAME`, `DESCRIPCION`, `FK_TS`) VALUES
(1, 'Transformación', '', 0),
(2, 'Math', '', 0),
(3, 'Suavizado', '', 1),
(4, 'Lag', '', 1),
(5, 'ACF', '', 2),
(6, 'Diferenciación', '', 2),
(7, 'T.Logaritmica', '', 2),
(8, 'Dif. Log.', '', 2);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `datamining`
--
ALTER TABLE `datamining`
  ADD PRIMARY KEY (`PK_DM`);

--
-- Indices de la tabla `indicadores`
--
ALTER TABLE `indicadores`
  ADD PRIMARY KEY (`PK_INDICADORE`);

--
-- Indices de la tabla `mlalgos`
--
ALTER TABLE `mlalgos`
  ADD PRIMARY KEY (`PK_ML`);

--
-- Indices de la tabla `tickers`
--
ALTER TABLE `tickers`
  ADD PRIMARY KEY (`PK_TICKER`);

--
-- Indices de la tabla `tipo`
--
ALTER TABLE `tipo`
  ADD PRIMARY KEY (`PK_TIPO`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `datamining`
--
ALTER TABLE `datamining`
  MODIFY `PK_DM` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `indicadores`
--
ALTER TABLE `indicadores`
  MODIFY `PK_INDICADORE` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `mlalgos`
--
ALTER TABLE `mlalgos`
  MODIFY `PK_ML` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tickers`
--
ALTER TABLE `tickers`
  MODIFY `PK_TICKER` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
